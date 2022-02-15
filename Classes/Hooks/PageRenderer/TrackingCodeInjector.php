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
use Brotkrueml\MatomoIntegration\Code\ScriptTagBuilder;
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
    private ScriptTagBuilder $scriptTagBuilder;

    /**
     * Parameter for testing purposes only!
     */
    public function __construct(
        ?ApplicationType $applicationType = null,
        ?ServerRequestInterface $request = null,
        ?JavaScriptTrackingCodeBuilder $javaScriptTrackingCodeBuilder = null,
        ?NoScriptTrackingCodeBuilder $noScriptTrackingCodeBuilder = null,
        ?TagManagerCodeBuilder $tagManagerCodeBuilder = null,
        ?ScriptTagBuilder $scriptTagBuilder = null
    ) {
        $this->applicationType = $applicationType ?? new ApplicationType();
        $this->request = $request ?? $this->getRequest();
        $this->javaScriptTrackingCodeBuilder = $javaScriptTrackingCodeBuilder ?? GeneralUtility::makeInstance(JavaScriptTrackingCodeBuilder::class);
        $this->noScriptTrackingCodeBuilder = $noScriptTrackingCodeBuilder ?? GeneralUtility::makeInstance(NoScriptTrackingCodeBuilder::class);
        $this->tagManagerCodeBuilder = $tagManagerCodeBuilder ?? GeneralUtility::makeInstance(TagManagerCodeBuilder::class);
        $this->scriptTagBuilder = $scriptTagBuilder ?? GeneralUtility::makeInstance(ScriptTagBuilder::class);
        $this->scriptTagBuilder->setRequest($this->request);
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function execute(?array &$params, PageRenderer $pageRenderer): void
    {
        if ($this->applicationType->isBackend()) {
            return;
        }

        /** @var Site $site */
        $site = $this->request->getAttribute('site');
        $configuration = Configuration::createFromSiteConfiguration($site->getConfiguration());

        if (! $this->hasValidConfiguration($configuration)) {
            return;
        }

        $scriptCode = $this->javaScriptTrackingCodeBuilder->setConfiguration($configuration)->getTrackingCode();
        if ($configuration->tagManagerContainerId !== '') {
            $scriptCode .= $this->tagManagerCodeBuilder->setConfiguration($configuration)->getCode();
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
