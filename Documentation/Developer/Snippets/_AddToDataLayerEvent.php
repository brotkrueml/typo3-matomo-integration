<?php

declare(strict_types=1);

namespace YourVender\YourExtension\Matomo;

use Brotkrueml\MatomoIntegration\Event\AddToDataLayerEvent;

final class AddOrderDetailsToDataLayerExample
{
    public function __invoke(AddToDataLayerEvent $event): void
    {
        $event->addVariable('orderTotal', 4545.45);
        $event->addVariable('orderCurrency', 'EUR');
    }
}
