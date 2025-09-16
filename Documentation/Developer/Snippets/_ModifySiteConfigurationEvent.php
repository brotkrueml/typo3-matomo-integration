<?php

declare(strict_types=1);

namespace YourVender\YourExtension\Matomo;

use Brotkrueml\MatomoIntegration\Event\ModifySiteConfigurationEvent;

final class ModifyMatomoSiteId
{
    public function __invoke(ModifySiteConfigurationEvent $event): void
    {
        if ($event->getRequest()->getAttribute('language')->getLanguageId() === 1) {
            // Override the site ID when in another language
            $event->setSiteId(42);
        }
    }
}
