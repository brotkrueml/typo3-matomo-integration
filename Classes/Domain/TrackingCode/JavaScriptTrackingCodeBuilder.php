<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Domain\TrackingCode;

use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Entity\CustomDimension;
use Brotkrueml\MatomoIntegration\Event\AfterTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

/**
 * @internal
 */
class JavaScriptTrackingCodeBuilder
{
    private Configuration $configuration;
    private EventDispatcher $eventDispatcher;
    private array $trackingCodeParts = [];

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setConfiguration(Configuration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
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
        /** @var BeforeTrackPageViewEvent $event */
        $event = $this->eventDispatcher->dispatch(new BeforeTrackPageViewEvent());
        $this->trackingCodeParts[] = $event->getCode();
    }

    private function addTrackPageView(): void
    {
        /** @var EnrichTrackPageViewEvent $event */
        $event = $this->eventDispatcher->dispatch(new EnrichTrackPageViewEvent());
        $pageTitle = $event->getPageTitle();
        $customDimensions = $event->getCustomDimensions();

        $arguments = ['"trackPageView"'];
        if ($pageTitle !== '' || $customDimensions !== []) {
            $arguments[] = '"' . $this->escapeDoubleQuotes($pageTitle) . '"';
        }
        if ($customDimensions !== []) {
            $arguments[] = $this->buildCustomDimensionsJson($customDimensions);
        }

        $this->trackingCodeParts[] = \sprintf('_paq.push([%s]);', \implode(',', $arguments));
    }

    private function escapeDoubleQuotes(string $value): string
    {
        return \str_replace('"', '\"', $value);
    }

    /**
     * @param CustomDimension[] $customDimensions
     */
    private function buildCustomDimensionsJson(array $customDimensions): string
    {
        $customDimensionsArray = [];
        foreach ($customDimensions as $customDimension) {
            $customDimensionsArray['dimension' . $customDimension->getId()] = $customDimension->getValue();
        }

        return \json_encode($customDimensionsArray, \JSON_THROW_ON_ERROR);
    }

    private function dispatchAfterTrackPageViewEvent(): void
    {
        /** @var AfterTrackPageViewEvent $event */
        $event = $this->eventDispatcher->dispatch(new AfterTrackPageViewEvent());
        $this->trackingCodeParts[] = $event->getCode();
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
