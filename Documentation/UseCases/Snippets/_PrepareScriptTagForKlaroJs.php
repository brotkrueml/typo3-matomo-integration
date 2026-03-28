<?php

declare(strict_types=1);

namespace YourVendor\YourExtension\EventListener;

use Brotkrueml\MatomoIntegration\Event\EnrichScriptTagEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
    identifier: 'your-ext/prepare-script-for-klaro-js',
)]
final readonly class PrepareScriptTagForKlaroJs
{
    public function __invoke(EnrichScriptTagEvent $event)
    {
        $event->setType('text/plain');
        $event->addDataAttribute('type', 'application/javascript');
        $event->addDataAttribute('name', 'matomo');
    }
}
