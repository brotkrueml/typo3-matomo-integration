<?php

declare(strict_types=1);

namespace YourVendor\YourExtension\EventListener;

use Brotkrueml\MatomoIntegration\Code\JavaScriptCode;
use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
    identifier: 'your-ext/add-colour-scheme-to-matomo-tracking',
)]
final readonly class AddColourSchemeToMatomoTracking
{
    private int $customDimensionId;

    public function __construct(int $customDimensionId)
    {
        $this->customDimensionId = $customDimensionId;
    }

    public function __invoke(BeforeTrackPageViewEvent $event): void
    {
        $event->addMatomoMethodCall(
            'setCustomDimension',
            $this->customDimensionId,
            new JavaScriptCode('window.matchMedia&&window.matchMedia("(prefers-color-scheme:dark)").matches?"dark":"light"'),
        );
    }
}
