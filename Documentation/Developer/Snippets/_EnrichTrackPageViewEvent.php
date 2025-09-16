<?php

declare(strict_types=1);

namespace YourVender\YourExtension\Matomo;

use Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent;

final class SomeEnrichTrackPageViewExample
{
    public function __invoke(EnrichTrackPageViewEvent $event): void
    {
        // You can set another page title
        $event->setPageTitle('Some Page Title');
        // And/or you can set a custom dimension only for the track page view call
        $event->addCustomDimension(3, 'Some Custom Dimension Value');
    }
}
