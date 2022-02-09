<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Code;

use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Entity\CustomDimension;
use Brotkrueml\MatomoIntegration\Event\AfterTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\TrackSiteSearchEvent;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

/**
 * @internal
 */
class JavaScriptTrackingCodeBuilder
{
    private Configuration $configuration;
    private EventDispatcher $eventDispatcher;
    /**
     * @var list<JavaScriptCode|MatomoMethodCall>
     */
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
        $trackSiteSearchEnabled = $this->addTrackSiteSearch();
        if (! $trackSiteSearchEnabled) {
            $this->addTrackPageView();
        }
        $this->dispatchAfterTrackPageViewEvent();
        $this->addTracker();

        return \implode('', $this->trackingCodeParts);
    }

    private function initialiseTrackingCode(): void
    {
        $this->trackingCodeParts[] = new JavaScriptCode('var _paq=window._paq||[];');
    }

    private function dispatchBeforeTrackPageViewEvent(): void
    {
        /** @var BeforeTrackPageViewEvent $event */
        $event = $this->eventDispatcher->dispatch(new BeforeTrackPageViewEvent($this->configuration));
        $this->trackingCodeParts = \array_merge(
            $this->trackingCodeParts,
            $event->getJavaScriptCodes(),
            $event->getMatomoMethodCalls()
        );
    }

    private function addTrackSiteSearch(): bool
    {
        /** @var TrackSiteSearchEvent $event */
        $event = $this->eventDispatcher->dispatch(new TrackSiteSearchEvent());
        $keyword = $event->getKeyword();
        if ($keyword === '') {
            return false;
        }

        $category = $event->getCategory();
        $searchCount = $event->getSearchCount();
        $customDimensions = $event->getCustomDimensions();

        $parameters = [$keyword];
        if ($category !== false || $searchCount !== false || $customDimensions !== []) {
            $parameters[] = $category;
        }
        if ($searchCount !== false || $customDimensions !== []) {
            $parameters[] = $searchCount;
        }
        if ($customDimensions !== []) {
            $parameters[] = $this->buildCustomDimensionsJson($customDimensions);
        }

        $this->trackingCodeParts[] = new MatomoMethodCall('trackSiteSearch', ...$parameters);

        return true;
    }

    private function addTrackPageView(): void
    {
        /** @var EnrichTrackPageViewEvent $event */
        $event = $this->eventDispatcher->dispatch(new EnrichTrackPageViewEvent());
        $pageTitle = $event->getPageTitle();
        $customDimensions = $event->getCustomDimensions();

        $parameters = [];
        if ($pageTitle !== '' || $customDimensions !== []) {
            $parameters[] = $pageTitle;
        }
        if ($customDimensions !== []) {
            $parameters[] = $this->buildCustomDimensionsJson($customDimensions);
        }

        $this->trackingCodeParts[] = new MatomoMethodCall('trackPageView', ...$parameters);
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
        $event = $this->eventDispatcher->dispatch(new AfterTrackPageViewEvent($this->configuration));
        $this->trackingCodeParts = \array_merge(
            $this->trackingCodeParts,
            $event->getJavaScriptCodes(),
            $event->getMatomoMethodCalls()
        );
    }

    private function addTracker(): void
    {
        $this->trackingCodeParts[] = new JavaScriptCode(
            '(function(){'
            . \sprintf('var u="%s";', $this->configuration->url)
            . '_paq.push(["setTrackerUrl",u+"matomo.php"]);'
            . \sprintf('_paq.push(["setSiteId",%d]);', $this->configuration->siteId)
            . 'var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];'
            . 'g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);'
            . '})();'
        );
    }
}
