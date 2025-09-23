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
use Brotkrueml\MatomoIntegration\EventListener\TrackErrorPages;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\Site;

final class TrackErrorPagesTest extends TestCase
{
    /**
     * @var Stub&Site
     */
    private Stub $siteStub;

    /**
     * @var Stub&PageArguments
     */
    private Stub $pageArgumentsStub;

    /**
     * @var Stub&ServerRequestInterface
     */
    private Stub $requestStub;

    private TrackErrorPages $subject;

    protected function setUp(): void
    {
        $this->siteStub = self::createStub(Site::class);
        $this->pageArgumentsStub = self::createStub(PageArguments::class);
        $this->requestStub = self::createStub(ServerRequestInterface::class);
        $this->subject = new TrackErrorPages();
    }

    #[Test]
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

    #[Test]
    public function enabledOptionWithoutErrorHandlingDefined(): void
    {
        $this->siteStub
            ->method('getConfiguration')
            ->willReturn([]);

        $this->requestStub
            ->method('getAttribute')
            ->with('site')
            ->willReturn($this->siteStub);

        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
            'matomoIntegrationOptions' => 'trackErrorPages',
        ]);

        $event = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(0, $actual);
    }

    #[Test]
    public function enabledOptionWithErrorHandlingDefinedAndPageIdDoesNotMatch(): void
    {
        $this->siteStub
            ->method('getConfiguration')
            ->willReturn([
                'errorHandling' => [[
                    'errorCode' => 404,
                    'errorHandler' => 'Page',
                    'errorContentSource' => 't3://page?uid=13',
                ]],
            ]);

        $this->pageArgumentsStub
            ->method('getPageId')
            ->willReturn(42);

        $map = [
            ['site', null, $this->siteStub],
            ['routing', null, $this->pageArgumentsStub],
        ];

        $this->requestStub
            ->method('getAttribute')
            ->willReturnMap($map);

        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
            'matomoIntegrationOptions' => 'trackErrorPages',
        ]);

        $event = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(0, $actual);
    }

    #[Test]
    public function enabledOptionWithErrorHandlingDefinedAndPageIdDoesMatch(): void
    {
        $this->siteStub
            ->method('getConfiguration')
            ->willReturn([
                'errorHandling' => [[
                    'errorCode' => 404,
                    'errorHandler' => 'Page',
                    'errorContentSource' => 't3://page?uid=42',
                ]],
            ]);

        $this->pageArgumentsStub
            ->method('getPageId')
            ->willReturn(42);

        $map = [
            ['site', null, $this->siteStub],
            ['routing', null, $this->pageArgumentsStub],
        ];

        $this->requestStub
            ->method('getAttribute')
            ->willReturnMap($map);

        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
            'matomoIntegrationOptions' => 'trackErrorPages',
        ]);

        $event = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(1, $actual);
        self::assertSame(
            '_paq.push(["setDocumentTitle","404/URL = "+encodeURIComponent(document.location.pathname+document.location.search)+" /From = "+encodeURIComponent(document.referrer)+""]);',
            (string) $actual[0],
        );
    }

    #[Test]
    public function enabledOptionWithErrorHandlingDefinedAndTwoPageIdsDoMatchTheFirstOneIsUsed(): void
    {
        $this->siteStub
            ->method('getConfiguration')
            ->willReturn([
                'errorHandling' => [[
                    'errorCode' => 403,
                    'errorHandler' => 'Page',
                    'errorContentSource' => 't3://page?uid=42',
                ], [
                    'errorCode' => 404,
                    'errorHandler' => 'Page',
                    'errorContentSource' => 't3://page?uid=42',
                ]],
            ]);

        $this->pageArgumentsStub
            ->method('getPageId')
            ->willReturn(42);

        $map = [
            ['site', null, $this->siteStub],
            ['routing', null, $this->pageArgumentsStub],
        ];

        $this->requestStub
            ->method('getAttribute')
            ->willReturnMap($map);

        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
            'matomoIntegrationOptions' => 'trackErrorPages',
        ]);

        $event = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(1, $actual);
        self::assertSame(
            '_paq.push(["setDocumentTitle","403/URL = "+encodeURIComponent(document.location.pathname+document.location.search)+" /From = "+encodeURIComponent(document.referrer)+""]);',
            (string) $actual[0],
        );
    }

    #[Test]
    public function enabledOptionWithErrorHandlingDefinedAndPageIdDoesMatchWithEmptyErrorPagesTemplateUsesDefaultTemplate(): void
    {
        $this->siteStub
            ->method('getConfiguration')
            ->willReturn([
                'errorHandling' => [[
                    'errorCode' => 404,
                    'errorHandler' => 'Page',
                    'errorContentSource' => 't3://page?uid=42',
                ]],
            ]);

        $this->pageArgumentsStub
            ->method('getPageId')
            ->willReturn(42);

        $map = [
            ['site', null, $this->siteStub],
            ['routing', null, $this->pageArgumentsStub],
        ];

        $this->requestStub
            ->method('getAttribute')
            ->willReturnMap($map);

        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
            'matomoIntegrationOptions' => 'trackErrorPages',
            'matomoIntegrationErrorPagesTemplate' => '',
        ]);

        $event = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(1, $actual);
        self::assertSame(
            '_paq.push(["setDocumentTitle","404/URL = "+encodeURIComponent(document.location.pathname+document.location.search)+" /From = "+encodeURIComponent(document.referrer)+""]);',
            (string) $actual[0],
        );
    }

    #[Test]
    public function enabledOptionWithErrorHandlingDefinedAndPageIdDoesMatchAndCustomTemplateDefined(): void
    {
        $this->siteStub
            ->method('getConfiguration')
            ->willReturn([
                'errorHandling' => [[
                    'errorCode' => 404,
                    'errorHandler' => 'Page',
                    'errorContentSource' => 't3://page?uid=42',
                ]],
            ]);

        $this->pageArgumentsStub
            ->method('getPageId')
            ->willReturn(42);

        $map = [
            ['site', null, $this->siteStub],
            ['routing', null, $this->pageArgumentsStub],
        ];

        $this->requestStub
            ->method('getAttribute')
            ->willReturnMap($map);

        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
            'matomoIntegrationOptions' => 'trackErrorPages',
            'matomoIntegrationErrorPagesTemplate' => '--{statusCode} | "{path}" | {referrer}--',
        ]);

        $event = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(1, $actual);
        self::assertSame(
            '_paq.push(["setDocumentTitle","--404 | \""+encodeURIComponent(document.location.pathname+document.location.search)+"\" | "+encodeURIComponent(document.referrer)+"--"]);',
            (string) $actual[0],
        );
    }

    #[Test]
    public function enabledOptionWithErrorHandlingAndMultipleErrorCodesDefinedAndPageIdDoesMatchAndCustomTemplateDefined(): void
    {
        $this->siteStub
            ->method('getConfiguration')
            ->willReturn([
                'errorHandling' => [
                    [
                        'errorCode' => 404,
                        'errorHandler' => 'Page',
                        'errorContentSource' => 't3://page?uid=41',
                    ],
                    [
                        'errorCode' => 500,
                        'errorHandler' => 'Page',
                        'errorContentSource' => 't3://page?uid=42',
                    ],
                ],
            ]);

        $this->pageArgumentsStub
            ->method('getPageId')
            ->willReturn(42);

        $map = [
            ['site', null, $this->siteStub],
            ['routing', null, $this->pageArgumentsStub],
        ];

        $this->requestStub
            ->method('getAttribute')
            ->willReturnMap($map);

        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
            'matomoIntegrationOptions' => 'trackErrorPages',
            'matomoIntegrationErrorPagesTemplate' => '--{statusCode} | "{path}" | {referrer}--',
        ]);

        $event = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $this->subject->__invoke($event);

        $actual = $event->getMatomoMethodCalls();
        self::assertCount(1, $actual);
        self::assertSame(
            '_paq.push(["setDocumentTitle","--500 | \""+encodeURIComponent(document.location.pathname+document.location.search)+"\" | "+encodeURIComponent(document.referrer)+"--"]);',
            (string) $actual[0],
        );
    }
}
