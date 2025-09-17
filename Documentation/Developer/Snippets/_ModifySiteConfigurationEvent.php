<?php

declare(strict_types=1);

namespace YourVender\YourExtension\Matomo;

use Brotkrueml\MatomoIntegration\Event\ModifySiteConfigurationEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
    identifier: 'your-vendor/your-extension/modify-matomo-site-id',
)]
final readonly class ModifyMatomoSiteId
{
    public function __invoke(ModifySiteConfigurationEvent $event): void
    {
        if ($event->getRequest()->getAttribute('language')->getLanguageId() === 1) {
            // Override the site ID when in another language
            $event->setSiteId(42);
        }
    }
}
