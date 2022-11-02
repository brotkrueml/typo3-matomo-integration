<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\EventListener;

use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Event\AfterTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\EventListener\TrackVisibleContentImpressions;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class TrackVisibleContentImpressionsTest extends TestCase
{
    /**
     * @var Stub&ServerRequestInterface
     */
    private $requestStub;
    private TrackVisibleContentImpressions $subject;

    protected function setUp(): void
    {
        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->subject = new TrackVisibleContentImpressions();
    }

    /**
     * @test
     */
    public function disabledOption(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
        ]);

        $event = new AfterTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(0, $actual);
    }

    /**
     * @test
     */
    public function enabledOption(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
            'matomoIntegrationOptions' => 'trackVisibleContentImpressions',
        ]);

        $event = new AfterTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(1, $actual);
        self::assertSame('_paq.push(["trackVisibleContentImpressions"]);', (string)$actual[0]);
    }
}
