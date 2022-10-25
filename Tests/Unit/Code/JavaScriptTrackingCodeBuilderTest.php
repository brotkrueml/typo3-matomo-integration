<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Code;

use Brotkrueml\MatomoIntegration\Code\JavaScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Event\AfterTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\TrackSiteSearchEvent;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

final class JavaScriptTrackingCodeBuilderTest extends TestCase
{
    /**
     * @var Stub&EventDispatcherInterface
     */
    private $eventDispatcherStub;
    /**
     * @var Stub&ServerRequestInterface
     */
    private $requestStub;
    private JavaScriptTrackingCodeBuilder $subject;

    protected function setUp(): void
    {
        $this->eventDispatcherStub = $this->createStub(EventDispatcherInterface::class);
        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->subject = new JavaScriptTrackingCodeBuilder($this->eventDispatcherStub);
    }

    /**
     * @test
     */
    public function getTrackingCodeWithMinimumConfigurationAndNoEventListenersReturnsTrackingCodeCorrectly(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
        ]);

        $eventDispatcher = new class() implements EventDispatcherInterface {
            public function dispatch(object $event, string $eventName = null): object
            {
                return $event;
            }
        };

        $subject = new JavaScriptTrackingCodeBuilder($eventDispatcher);
        $subject->setConfiguration($configuration);
        $subject->setRequest($this->requestStub);

        self::assertSame(
            'var _paq=window._paq||[];_paq.push(["trackPageView"]);(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();',
            $subject->getTrackingCode()
        );
    }

    /**
     * @test
     */
    public function getTrackingCodeReturnsCodeWithDispatchedBeforeTrackPageViewEventCorrectly(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
        ]);

        $beforeTrackPageViewEvent = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $beforeTrackPageViewEvent->addJavaScriptCode('/* some code */');
        $beforeTrackPageViewEvent->addMatomoMethodCall('someMethodCall');

        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturnOnConsecutiveCalls(
                $beforeTrackPageViewEvent,
                new TrackSiteSearchEvent($this->requestStub),
                new EnrichTrackPageViewEvent($this->requestStub),
                new AfterTrackPageViewEvent($configuration, $this->requestStub),
            );

        $this->subject
            ->setRequest($this->requestStub)
            ->setConfiguration($configuration);

        self::assertStringContainsString('/* some code */_paq.push(["someMethodCall"]);_paq.push(["trackPageView"]);', $this->subject->getTrackingCode());
    }

    /**
     * @test
     * @dataProvider dataProviderForGetTrackingCodeReturnsCodeWithTrackSiteSearchEventCorrectly
     */
    public function getTrackingCodeReturnsCodeWithTrackSiteSearchEventCorrectly(
        string $keyword,
        $category,
        $searchCount,
        array $customDimensions,
        string $expected
    ): void {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
        ]);

        $trackSiteSearchEvent = new TrackSiteSearchEvent($this->requestStub);
        $trackSiteSearchEvent->setKeyword($keyword);
        $trackSiteSearchEvent->setCategory($category);
        $trackSiteSearchEvent->setSearchCount($searchCount);
        foreach ($customDimensions as $customDimension) {
            $trackSiteSearchEvent->addCustomDimension($customDimension['id'], $customDimension['value']);
        }

        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturnOnConsecutiveCalls(
                new BeforeTrackPageViewEvent($configuration, $this->requestStub),
                $trackSiteSearchEvent,
                new AfterTrackPageViewEvent($configuration, $this->requestStub),
            );

        $this->subject
            ->setRequest($this->requestStub)
            ->setConfiguration($configuration);

        self::assertStringContainsString($expected, $this->subject->getTrackingCode());
    }

    public function dataProviderForGetTrackingCodeReturnsCodeWithTrackSiteSearchEventCorrectly(): iterable
    {
        yield 'Only keyword is given' => [
            'keyword' => 'some keyword',
            'category' => false,
            'searchCount' => false,
            'customDimension' => [],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","some keyword"]);(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();',
        ];

        yield 'Keyword and category are given' => [
            'keyword' => 'some keyword',
            'category' => 'some category',
            'searchCount' => false,
            'customDimension' => [],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","some keyword","some category"]);(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();',
        ];

        yield 'Keyword and search count are given' => [
            'keyword' => 'some keyword',
            'category' => false,
            'searchCount' => 42,
            'customDimension' => [],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","some keyword",false,42]);(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();',
        ];

        yield 'Keyword, category search count are given' => [
            'keyword' => 'some keyword',
            'category' => 'some category',
            'searchCount' => 42,
            'customDimension' => [],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","some keyword","some category",42]);(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();',
        ];

        yield 'Search count of 0 is given' => [
            'keyword' => 'some keyword',
            'category' => false,
            'searchCount' => 0,
            'customDimension' => [],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","some keyword",false,0]);(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();',
        ];

        yield 'Keyword and one custom dimension are given' => [
            'keyword' => 'some keyword',
            'category' => false,
            'searchCount' => false,
            'customDimension' => [
                [
                    'id' => 1,
                    'value' => 'some custom dimension',
                ],
            ],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","some keyword",false,false,{"dimension1":"some custom dimension"}]);(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();',
        ];

        yield 'Keyword and two custom dimensions are given' => [
            'keyword' => 'some keyword',
            'category' => false,
            'searchCount' => false,
            'customDimension' => [
                [
                    'id' => 1,
                    'value' => 'some custom dimension',
                ],
                [
                    'id' => 2,
                    'value' => 'another custom dimension',
                ],
            ],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","some keyword",false,false,{"dimension1":"some custom dimension","dimension2":"another custom dimension"}]);(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();',
        ];

        yield 'Keyword, category, search count and one custom dimension are given' => [
            'keyword' => 'some keyword',
            'category' => 'some category',
            'searchCount' => 123,
            'customDimension' => [
                [
                    'id' => 1,
                    'value' => 'some custom dimension',
                ],
            ],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","some keyword","some category",123,{"dimension1":"some custom dimension"}]);(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();',
        ];

        yield 'Keyword provoking XSS' => [
            'keyword' => '</script><svg/onload=prompt(document.domain)',
            'category' => false,
            'searchCount' => false,
            'customDimension' => [],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","\u003C\/script\u003E\u003Csvg\/onload=prompt(document.domain)"]);',
        ];

        yield 'Category provoking XSS' => [
            'keyword' => 'some keyword',
            'category' => '</script><svg/onload=prompt(document.domain)',
            'searchCount' => false,
            'customDimension' => [],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","some keyword","\u003C\/script\u003E\u003Csvg\/onload=prompt(document.domain)"]);',
        ];

        yield 'Search count provoking XSS' => [
            'keyword' => 'some keyword',
            'category' => false,
            'searchCount' => '</script><svg/onload=prompt(document.domain)',
            'customDimension' => [],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","some keyword",false,"\u003C\/script\u003E\u003Csvg\/onload=prompt(document.domain)"]);',
        ];

        yield 'Custom dimension provoking XSS' => [
            'keyword' => 'some keyword',
            'category' => false,
            'searchCount' => false,
            'customDimension' => [
                [
                    'id' => 1,
                    'value' => '</script><svg/onload=prompt(document.domain)',
                ],
            ],
            'expected' => 'var _paq=window._paq||[];_paq.push(["trackSiteSearch","some keyword",false,false,{"dimension1":"\u003C\/script\u003E\u003Csvg\/onload=prompt(document.domain)"}]);',
        ];
    }

    /**
     * @test
     */
    public function getTrackingCodeReturnsCodeWithTrackSiteSearchEventAndBeforeAndAfterTrackPageViewEventCorrectly(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
        ]);

        $trackSiteSearchEvent = new TrackSiteSearchEvent($this->requestStub);
        $trackSiteSearchEvent->setKeyword('some keyword');

        $beforeTrackPageViewEvent = new BeforeTrackPageViewEvent($configuration, $this->requestStub);
        $beforeTrackPageViewEvent->addJavaScriptCode('/* some code before */');

        $afterTrackPageViewEvent = new AfterTrackPageViewEvent($configuration, $this->requestStub);
        $afterTrackPageViewEvent->addJavaScriptCode('/* some code after */');

        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturnOnConsecutiveCalls(
                $beforeTrackPageViewEvent,
                $trackSiteSearchEvent,
                $afterTrackPageViewEvent,
            );

        $this->subject
            ->setRequest($this->requestStub)
            ->setConfiguration($configuration);

        self::assertStringContainsString(
            'var _paq=window._paq||[];/* some code before */_paq.push(["trackSiteSearch","some keyword"]);/* some code after */(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();',
            $this->subject->getTrackingCode()
        );
    }

    /**
     * @test
     * @dataProvider dataProviderForGetTrackingCodeWithDispatchedEnrichTrackPageView
     */
    public function getTrackingCodeReturnsCodeWithDispatchedEnrichTrackPageViewEventCorrectly(
        string $pageTitle,
        array $customDimensions,
        string $expected
    ): void {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
        ]);

        $enrichTrackPageViewEvent = new EnrichTrackPageViewEvent($this->requestStub);
        $enrichTrackPageViewEvent->setPageTitle($pageTitle);
        foreach ($customDimensions as $customDimension) {
            $enrichTrackPageViewEvent->addCustomDimension(...$customDimension);
        }

        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturnOnConsecutiveCalls(
                new BeforeTrackPageViewEvent($configuration, $this->requestStub),
                new TrackSiteSearchEvent($this->requestStub),
                $enrichTrackPageViewEvent,
                new AfterTrackPageViewEvent($configuration, $this->requestStub),
            );

        $this->subject
            ->setRequest($this->requestStub)
            ->setConfiguration($configuration);

        self::assertStringContainsString($expected, $this->subject->getTrackingCode());
    }

    public function dataProviderForGetTrackingCodeWithDispatchedEnrichTrackPageView(): iterable
    {
        yield 'without page title and without custom dimensions' => [
            '',
            [],
            '_paq.push(["trackPageView"]);',
        ];

        yield 'with page title and without custom dimensions' => [
            'some page title',
            [],
            '_paq.push(["trackPageView","some page title"]);',
        ];

        yield 'with page title and with one custom dimension' => [
            'some page title',
            [[1, 'some custom dimension value']],
            '_paq.push(["trackPageView","some page title",{"dimension1":"some custom dimension value"}]);',
        ];

        yield 'with page title and with two custom dimensions' => [
            'some page title',
            [
                [1, 'some custom dimension value'],
                [2, 'another custom dimension value'],
            ],
            '_paq.push(["trackPageView","some page title",{"dimension1":"some custom dimension value","dimension2":"another custom dimension value"}]);',
        ];

        yield 'without page title and with one custom dimension' => [
            '',
            [[1, 'some custom dimension value']],
            '_paq.push(["trackPageView","",{"dimension1":"some custom dimension value"}]);',
        ];

        yield 'with page title which has double quotes' => [
            'some "page title"',
            [],
            '_paq.push(["trackPageView","some \u0022page title\u0022"]);',
        ];

        yield 'with custom dimension which has double quotes in value' => [
            '',
            [[1, 'some "custom dimension" value']],
            '_paq.push(["trackPageView","",{"dimension1":"some \u0022custom dimension\u0022 value"}]);',
        ];

        yield 'with page title provoking XSS' => [
            '</script><svg/onload=prompt(document.domain)>',
            [],
            'var _paq=window._paq||[];_paq.push(["trackPageView","\u003C\/script\u003E\u003Csvg\/onload=prompt(document.domain)\u003E"]);',
        ];

        yield 'with custom dimension provoking XSS' => [
            '',
            [[1, '</script><svg/onload=prompt(document.domain)>']],
            '_paq.push(["trackPageView","",{"dimension1":"\u003C\/script\u003E\u003Csvg\/onload=prompt(document.domain)\u003E"}]);',
        ];
    }

    /**
     * @test
     */
    public function getTrackingCodeReturnsCodeWithDispatchedAfterTrackPageViewEventCorrectly(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
        ]);

        $afterTrackPageViewEvent = new AfterTrackPageViewEvent($configuration, $this->requestStub);
        $afterTrackPageViewEvent->addJavaScriptCode('/* some code */');
        $afterTrackPageViewEvent->addMatomoMethodCall('someMethodCall');

        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturnOnConsecutiveCalls(
                new BeforeTrackPageViewEvent($configuration, $this->requestStub),
                new TrackSiteSearchEvent($this->requestStub),
                new EnrichTrackPageViewEvent($this->requestStub),
                $afterTrackPageViewEvent,
            );

        $this->subject
            ->setRequest($this->requestStub)
            ->setConfiguration($configuration);

        self::assertStringContainsString('_paq.push(["trackPageView"]);/* some code */_paq.push(["someMethodCall"]);', $this->subject->getTrackingCode());
    }
}
