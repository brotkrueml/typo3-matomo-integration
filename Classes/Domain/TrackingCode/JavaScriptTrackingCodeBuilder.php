<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Domain\TrackingCode;

use Brotkrueml\MatomoIntegration\Domain\Dto\Configuration;

final class JavaScriptTrackingCodeBuilder
{
    private Configuration $configuration;
    private array $trackingCodeParts = [];

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getTrackingCode(): string
    {
        $this->initialiseTrackingCode();
        $this->dispatchBeforeTrackPageViewEvent();
        $this->addTrackPageView();
        $this->dispatchAfterTrackPageViewEvent();
        $this->considerLinkTracking();
        $this->considerPerformanceTracking();
        $this->considerHeartBeatTimer();
        $this->considerTrackAllContentImpressions();
        $this->considerTrackVisibleContentImpressions();
        $this->addTracker();

        return \implode('', $this->trackingCodeParts);
    }

    private function initialiseTrackingCode(): void
    {
        $this->trackingCodeParts[] = 'var _paq=window._paq||[];';
    }

    private function dispatchBeforeTrackPageViewEvent(): void
    {
        // todo
    }

    private function addTrackPageView(): void
    {
        $this->trackingCodeParts[] = '_paq.push(["trackPageView"]);';
    }

    private function dispatchAfterTrackPageViewEvent(): void
    {
        // todo
    }

    private function considerLinkTracking(): void
    {
        if ($this->configuration->linkTracking) {
            $this->trackingCodeParts[] = '_paq.push(["enableLinkTracking"]);';
        }
    }

    private function considerPerformanceTracking(): void
    {
        if (!$this->configuration->performanceTracking) {
            $this->trackingCodeParts[] = '_paq.push(["disablePerformanceTracking"]);';
        }
    }

    private function considerHeartBeatTimer(): void
    {
        if (!$this->configuration->heartBeatTimer) {
            return;
        }

        if (
            $this->configuration->heartBeatTimerActiveTimeInSeconds > 0 &&
            $this->configuration->heartBeatTimerActiveTimeInSeconds !== Configuration::HEART_BEAT_TIMER_DEFAULT_ACTIVE_TIME_IN_SECONDS
        ) {
            $this->trackingCodeParts[] = \sprintf(
                '_paq.push(["enableHeartBeatTimer", %d]);',
                $this->configuration->heartBeatTimerActiveTimeInSeconds
            );
            return;
        }

        $this->trackingCodeParts[] = '_paq.push(["enableHeartBeatTimer"]);';
    }

    private function considerTrackAllContentImpressions(): void
    {
        if ($this->configuration->trackAllContentImpressions) {
            $this->trackingCodeParts[] = '_paq.push(["trackAllContentImpressions"]);';
        }
    }

    private function considerTrackVisibleContentImpressions(): void
    {
        if ($this->configuration->trackVisibleContentImpressions) {
            $this->trackingCodeParts[] = '_paq.push(["trackVisibleContentImpressions"]);';
        }
    }

    private function addTracker(): void
    {
        $this->trackingCodeParts[] = '(function(){'
            . \sprintf('var u="%s";', \rtrim($this->configuration->url, '/') . '/', )
            . '_paq.push(["setTrackerUrl",u+"matomo.php"]);'
            . \sprintf('_paq.push(["setSiteId",%d]);', $this->configuration->siteId)
            . 'var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];'
            . 'g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);'
            . '})();';
    }
}
