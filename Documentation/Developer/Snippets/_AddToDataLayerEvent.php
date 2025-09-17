<?php

declare(strict_types=1);

namespace YourVender\YourExtension\Matomo;

use Brotkrueml\MatomoIntegration\Event\AddToDataLayerEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
    identifier: 'your-vendor/your-extension/add-order-details-to-datalayer-example',
)]
final readonly class AddOrderDetailsToDataLayerExample
{
    public function __invoke(AddToDataLayerEvent $event): void
    {
        $event->addVariable('orderTotal', 4545.45);
        $event->addVariable('orderCurrency', 'EUR');
    }
}
