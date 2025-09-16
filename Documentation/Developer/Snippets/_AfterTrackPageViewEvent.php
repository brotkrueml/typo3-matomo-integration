<?php

declare(strict_types=1);

namespace YourVender\YourExtension\Matomo;

use Brotkrueml\MatomoIntegration\Event\AfterTrackPageViewEvent;

final class EnableHeartBeatTimerWithActiveSecondsExample
{
    public function __invoke(AfterTrackPageViewEvent $event): void
    {
        $event->addMatomoMethodCall('enableHeartBeatTimer', 30);
    }
}
