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
use Brotkrueml\MatomoIntegration\EventListener\RequireConsent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(RequireConsent::class)]
final class RequireConsentTest extends TestCase
{
    /**
     * @var ServerRequestInterface&Stub
     */
    private Stub $requestStub;
    private RequireConsent $subject;

    protected function setUp(): void
    {
        $this->requestStub = self::createStub(ServerRequestInterface::class);
        $this->subject = new RequireConsent();
    }

    #[Test]
    public function disabledBothOptions(): void
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

    #[Test]
    public function enabledOptionRequireConsent(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
            'matomoIntegrationOptions' => 'requireConsent',
        ]);

        $event = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(1, $actual);
        self::assertSame('_paq.push(["requireConsent"]);', (string) $actual[0]);
    }

    #[Test]
    public function enabledOptionRequireCookieConsent(): void
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
        self::assertSame('_paq.push(["requireCookieConsent"]);', (string) $actual[0]);
    }

    #[Test]
    public function enabledBothOptionsRequireConsentAndRequireCookieConsentThenOnlyRequireConsentIsConsidered(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
            'matomoIntegrationOptions' => 'requireConsent,requireCookieConsent',
        ]);

        $event = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(1, $actual);
        self::assertSame('_paq.push(["requireConsent"]);', (string) $actual[0]);
    }
}
