<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Event;

use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Event\AbstractTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class BeforeTrackPageViewEventTest extends TestCase
{
    #[Test]
    public function classInheritsFromAbstractTrackPageViewEvent(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
        ]);
        $subject = new BeforeTrackPageViewEvent(
            $configuration,
            self::createStub(ServerRequestInterface::class),
        );

        self::assertInstanceOf(AbstractTrackPageViewEvent::class, $subject);
    }
}
