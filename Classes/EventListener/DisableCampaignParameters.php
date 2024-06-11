<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\EventListener;

use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;

/**
 * @internal
 */
final class DisableCampaignParameters
{
    public function __invoke(BeforeTrackPageViewEvent $event): void
    {
        if ($event->getConfiguration()->disableCampaignParameters) {
            $event->addMatomoMethodCall('disableCampaignParameters');
        }
    }
}
