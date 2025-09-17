<?php

declare(strict_types=1);

namespace YourVender\YourExtension\Matomo;

use Brotkrueml\MatomoIntegration\Event\EnrichScriptTagEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
    identifier: 'your-vendor/your-extension/add-attributes-to-matomo-script-tag',
)]
final readonly class AddAttributesToMatomoScriptTag
{
    public function __invoke(EnrichScriptTagEvent $event): void
    {
        // Set the id
        $event->setId('some-id');

        // Add data attributes
        $event->addDataAttribute('foo', 'bar');
        $event->addDataAttribute('qux');
    }
}
