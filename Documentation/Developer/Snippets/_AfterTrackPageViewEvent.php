<?php

declare(strict_types=1);

namespace YourVender\YourExtension\Matomo;

use Brotkrueml\MatomoIntegration\Event\AfterTrackPageViewEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
    identifier: 'your-vendor/your-extension/enable-heartbeat-timer-with-active-seconds-example',
)]
final readonly class EnableHeartBeatTimerWithActiveSecondsExample
{
    public function __invoke(AfterTrackPageViewEvent $event): void
    {
        $event->addMatomoMethodCall('enableHeartBeatTimer', 30);
    }
}
