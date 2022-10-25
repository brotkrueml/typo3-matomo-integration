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
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
final class TrackingCodeInjector
{
    private JavaScriptTrackingCodeBuilder $javaScriptTrackingCodeBuilder;
    private NoScriptTrackingCodeBuilder $noScriptTrackingCodeBuilder;
    private TagManagerCodeBuilder $tagManagerCodeBuilder;
    private ScriptTagBuilder $scriptTagBuilder;

    public function __construct(
        JavaScriptTrackingCodeBuilder $javaScriptTrackingCodeBuilder,
        NoScriptTrackingCodeBuilder $noScriptTrackingCodeBuilder,
        TagManagerCodeBuilder $tagManagerCodeBuilder,
        ScriptTagBuilder $scriptTagBuilder
    ) {
        $this->javaScriptTrackingCodeBuilder = $javaScriptTrackingCodeBuilder;
        $this->noScriptTrackingCodeBuilder = $noScriptTrackingCodeBuilder;
        $this->tagManagerCodeBuilder = $tagManagerCodeBuilder;
        $this->scriptTagBuilder = $scriptTagBuilder;
    }

    /**
     * @param array{}|null $params
     */
    public function execute(?array &$params, PageRenderer $pageRenderer): void
    {
        $request = $this->getRequest();
        if (ApplicationType::fromRequest($request)->isBackend()) {
            return;
        }

        /** @var Site $site */
        $site = $request->getAttribute('site');
        $configuration = Configuration::createFromSiteConfiguration($site->getConfiguration());
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
        if ($configuration->tagManagerContainerId !== '') {
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
        if (! \filter_var($configuration->url, \FILTER_VALIDATE_URL)) {
            return false;
        }

        return $configuration->siteId > 0;
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
