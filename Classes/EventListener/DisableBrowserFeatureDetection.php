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
    identifier: 'matomo-integration/disable-browser-feature-detection',
)]
final readonly class DisableBrowserFeatureDetection
{
    public function __invoke(BeforeTrackPageViewEvent $event): void
    {
        if ($event->getConfiguration()->disableBrowserFeatureDetection) {
            $event->addMatomoMethodCall('disableBrowserFeatureDetection');
        }
    }
}
