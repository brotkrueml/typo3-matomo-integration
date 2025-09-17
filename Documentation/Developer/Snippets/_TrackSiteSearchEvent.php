<?php

declare(strict_types=1);

namespace YourVender\YourExtension\Matomo;

use Brotkrueml\MatomoIntegration\Event\TrackSiteSearchEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
    identifier: 'your-vendor/your-extension/some-track-site-search-example',
)]
final readonly class SomeTrackSiteSearchExample
{
    public function __invoke(TrackSiteSearchEvent $event): void
    {
        $event->setKeyword('some search keyword');
        $event->setSearchCount(42);
        $event->addCustomDimension(3, 'some custom dimension value');
    }
}
