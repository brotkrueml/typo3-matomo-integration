<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Hooks\PageRenderer;

use Brotkrueml\MatomoIntegration\Adapter\ApplicationType;
use Brotkrueml\MatomoIntegration\Code\JavaScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Code\NoScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Code\TagManagerCodeBuilder;
use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
final class TrackingCodeInjector
{
    private ApplicationType $applicationType;
    private ServerRequestInterface $request;
    private JavaScriptTrackingCodeBuilder $javaScriptTrackingCodeBuilder;
    private NoScriptTrackingCodeBuilder $noScriptTrackingCodeBuilder;
    private TagManagerCodeBuilder $tagManagerCodeBuilder;

    /**
     * Parameter for testing purposes only!
     */
    public function __construct(
        ?ApplicationType $applicationType = null,
        ServerRequestInterface $request = null,
        JavaScriptTrackingCodeBuilder $javaScriptTrackingCodeBuilder = null,
        NoScriptTrackingCodeBuilder $noScriptTrackingCodeBuilder = null,
        TagManagerCodeBuilder $tagManagerCodeBuilder = null
    ) {
        $this->applicationType = $applicationType ?? new ApplicationType();
        $this->request = $request ?? $this->getRequest();
        $this->javaScriptTrackingCodeBuilder = $javaScriptTrackingCodeBuilder ?? GeneralUtility::makeInstance(JavaScriptTrackingCodeBuilder::class);
        $this->noScriptTrackingCodeBuilder = $noScriptTrackingCodeBuilder ?? GeneralUtility::makeInstance(NoScriptTrackingCodeBuilder::class);
        $this->tagManagerCodeBuilder = $tagManagerCodeBuilder ?? GeneralUtility::makeInstance(TagManagerCodeBuilder::class);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function execute(?array &$params, PageRenderer $pageRenderer): void
    {
        if ($this->applicationType->isBackend()) {
            return;
        }

        /** @var Site $site */
        $site = $this->request->getAttribute('site');
        $configuration = Configuration::createFromSiteConfiguration($site->getConfiguration());

        // todo Cache configuration for specific site

        if (!$this->hasValidConfiguration($configuration)) {
            return;
        }

        $code = $this->javaScriptTrackingCodeBuilder->setConfiguration($configuration)->getTrackingCode();
        if ($configuration->tagManagerContainerId !== '') {
            $code .= $this->tagManagerCodeBuilder->setConfiguration($configuration)->getCode();
        }

        $pageRenderer->addHeaderData(\sprintf('<script>%s</script>', $code));

        if ($configuration->noScript) {
            $pageRenderer->addFooterData(
                \sprintf(
                    '<noscript>%s</noscript>',
                    $this->noScriptTrackingCodeBuilder->setConfiguration($configuration)->getTrackingCode()
                )
            );
        }
    }

    private function hasValidConfiguration(Configuration $configuration): bool
    {
        if (!\filter_var($configuration->url, \FILTER_VALIDATE_URL)) {
            return false;
        }

        return $configuration->siteId > 0;
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
