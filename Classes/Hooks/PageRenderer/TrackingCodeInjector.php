<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Hooks\PageRenderer;

use Brotkrueml\MatomoIntegration\Code\JavaScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Code\NoScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Code\ScriptTagBuilder;
use Brotkrueml\MatomoIntegration\Code\TagManagerCodeBuilder;
use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Event\ModifySiteConfigurationEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
final class TrackingCodeInjector
{
    public function __construct(
        private readonly JavaScriptTrackingCodeBuilder $javaScriptTrackingCodeBuilder,
        private readonly NoScriptTrackingCodeBuilder $noScriptTrackingCodeBuilder,
        private readonly TagManagerCodeBuilder $tagManagerCodeBuilder,
        private readonly ScriptTagBuilder $scriptTagBuilder,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {}

    /**
     * @param array{}|null $params
     */
    public function execute(?array &$params, PageRenderer $pageRenderer): void
    {
        $request = $this->getRequest();
        if ($request->getAttribute('applicationType') !== 1) {
            // Not a frontend request
            return;
        }

        /** @var Site $site */
        $site = $request->getAttribute('site');
        $configuration = Configuration::createFromSiteConfiguration($site->getConfiguration());
        /** @var ModifySiteConfigurationEvent $modifySiteConfigurationEvent */
        $modifySiteConfigurationEvent = $this->eventDispatcher->dispatch(
            new ModifySiteConfigurationEvent($request, $configuration, $site->getIdentifier()),
        );
        $configuration = $modifySiteConfigurationEvent->getConfiguration();
        if (! $this->hasValidConfiguration($configuration)) {
            return;
        }

        $this->javaScriptTrackingCodeBuilder
            ->setRequest($request)
            ->setConfiguration($configuration);
        $this->tagManagerCodeBuilder
            ->setRequest($request)
            ->setConfiguration($configuration);
        $this->scriptTagBuilder->setRequest($request);

        $scriptCode = $this->javaScriptTrackingCodeBuilder->getTrackingCode();
        if ($configuration->tagManagerContainerIds !== []) {
            $scriptCode .= $this->tagManagerCodeBuilder->getCode();
        }

        $pageRenderer->addHeaderData($this->scriptTagBuilder->build($scriptCode));

        if ($configuration->noScript) {
            $noScriptCode = $this->noScriptTrackingCodeBuilder->setConfiguration($configuration)->getTrackingCode();
            $pageRenderer->addFooterData("<noscript>{$noScriptCode}</noscript>");
        }
    }

    private function hasValidConfiguration(Configuration $configuration): bool
    {
        $url = $configuration->url;
        if (\str_starts_with($url, '//')) {
            // We add a protocol just for validation
            $url = 'https:' . $url;
        }
        if (! \filter_var($url, \FILTER_VALIDATE_URL)) {
            return false;
        }

        return $configuration->siteId > 0;
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
