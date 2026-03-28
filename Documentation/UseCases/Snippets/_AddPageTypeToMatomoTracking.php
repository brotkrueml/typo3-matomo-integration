<?php

declare(strict_types=1);

namespace YourVendor\YourExtension\EventListener;

use Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
    identifier: 'your-ext/add-page-type-to-matomo-tracking',
)]
final readonly class AddPageTypeToMatomoTracking
{
    private int $customDimensionId;
    private array $pageTypes;

    /**
     * @param array<int, string> $pageTypes
     */
    public function __construct(
        int $customDimensionId,
        array $pageTypes,
    ) {
        $this->customDimensionId = $customDimensionId;
        $this->pageTypes = $pageTypes;
    }

    public function __invoke(EnrichTrackPageViewEvent $event): void
    {
        $rootLine = $event->getRequest()->getAttribute('frontend.page.information')->getRootLine();
        $pageIds = \array_keys($this->pageTypes);
        $hits = \array_filter(
            $rootLine,
            static fn(array $page): bool => \in_array($page['uid'], $pageIds),
        );
        if ($hits === []) {
            return;
        }

        $pageType = $this->pageTypes[\current($hits)['uid']];
        $event->addCustomDimension($this->customDimensionId, $pageType);
    }
}
