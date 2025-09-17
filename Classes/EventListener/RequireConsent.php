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
use TYPO3\CMS\Core\Attribute\AsEventListener;

/**
 * @internal
 */
#[AsEventListener(
    identifier: 'matomo-integration/require-consent',
)]
final readonly class RequireConsent
{
    public function __invoke(BeforeTrackPageViewEvent $event): void
    {
        if ($event->getConfiguration()->requireConsent) {
            $event->addMatomoMethodCall('requireConsent');
            // "requireConsent" is more restrictive, so this one wins
            // if both options are set.
            return;
        }

        if ($event->getConfiguration()->requireCookieConsent) {
            $event->addMatomoMethodCall('requireCookieConsent');
        }
    }
}
