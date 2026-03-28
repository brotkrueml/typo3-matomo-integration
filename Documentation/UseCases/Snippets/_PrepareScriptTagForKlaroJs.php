<?php

declare(strict_types=1);

namespace YourVendor\YourExtension\EventListener;

use Brotkrueml\MatomoIntegration\Event\EnrichScriptTagEvent;

final class PrepareScriptTagForKlaroJs
{
    public function __invoke(EnrichScriptTagEvent $event)
    {
        $event->setType('text/plain');
        $event->addDataAttribute('type', 'application/javascript');
        $event->addDataAttribute('name', 'matomo');
    }
}
