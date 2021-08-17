<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Event\BeforeTrackPageViewEvent;

use Brotkrueml\MatomoIntegration\Event\AbstractTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\AfterTrackPageViewEvent;
use PHPUnit\Framework\TestCase;

final class AfterTrackPageViewEventTest extends TestCase
{
    /**
     * @test
     */
    public function classInheritsFromAbstractTrackPageViewEvent(): void
    {
        $subject = new AfterTrackPageViewEvent();

        self::assertInstanceOf(AbstractTrackPageViewEvent::class, $subject);
    }
}
