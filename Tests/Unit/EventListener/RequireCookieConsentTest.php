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
use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\EventListener\RequireCookieConsent;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class RequireCookieConsentTest extends TestCase
{
    /**
     * @var ServerRequestInterface&Stub
     */
    private $requestStub;
    private RequireCookieConsent $subject;

    protected function setUp(): void
    {
        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->subject = new RequireCookieConsent();
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

        $event = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
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
            'matomoIntegrationOptions' => 'requireCookieConsent',
        ]);

        $event = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(1, $actual);
        self::assertSame('_paq.push(["requireCookieConsent"]);', (string)$actual[0]);
    }
}
