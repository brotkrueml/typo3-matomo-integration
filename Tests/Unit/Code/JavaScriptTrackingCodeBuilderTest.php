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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

final class JavaScriptTrackingCodeBuilderTest extends TestCase
{
    /** @var Stub|EventDispatcher */
    private $eventDispatcherStub;

    private JavaScriptTrackingCodeBuilder $subject;

    protected function setUp(): void
    {
        $this->eventDispatcherStub = $this->createStub(EventDispatcher::class);
        $this->subject = new JavaScriptTrackingCodeBuilder($this->eventDispatcherStub);
    }

    /**
     * @test
     * @dataProvider dataProviderForGetTrackingCode
     */
    public function getTrackingCodeReturnsTrackingCodeCorrectly(array $configuration, string $expected): void
    {
        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturnOnConsecutiveCalls(
                new BeforeTrackPageViewEvent(),
                new EnrichTrackPageViewEvent(),
                new AfterTrackPageViewEvent(),
            );

        $this->subject->setConfiguration(
            Configuration::createFromSiteConfiguration($configuration)
        );

        self::assertSame($expected, $this->subject->getTrackingCode());
    }

    public function dataProviderForGetTrackingCode(): iterable
    {
        $defaultConfiguration = [
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
        ];

        $expectedTrackPageView = 'var _paq=window._paq||[];_paq.push(["trackPageView"]);';
        $expectedTrackPageViewWithDisabledPerformanceTracking = $expectedTrackPageView . '_paq.push(["disablePerformanceTracking"]);';
        $expectedTracker = '(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();';

        yield 'Minimum configuration' => [
            $defaultConfiguration,
            $expectedTrackPageViewWithDisabledPerformanceTracking . $expectedTracker,
        ];

        yield 'With link tracking enabled' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationOptions' => 'linkTracking']),
            $expectedTrackPageView . '_paq.push(["enableLinkTracking"]);_paq.push(["disablePerformanceTracking"]);' . $expectedTracker,
        ];

        yield 'With performance tracking disabled' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationOptions' => 'performanceTracking']),
            $expectedTrackPageView . $expectedTracker,
        ];

        yield 'With heart beat timer enabled' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationOptions' => 'heartBeatTimer']),
            $expectedTrackPageViewWithDisabledPerformanceTracking . '_paq.push(["enableHeartBeatTimer"]);' . $expectedTracker,
        ];

        yield 'With track all content impressions enabled' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationOptions' => 'trackAllContentImpressions']),
            $expectedTrackPageViewWithDisabledPerformanceTracking . '_paq.push(["trackAllContentImpressions"]);' . $expectedTracker,
        ];

        yield 'With track visible content impressions enabled' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationOptions' => 'trackVisibleContentImpressions']),
            $expectedTrackPageViewWithDisabledPerformanceTracking . '_paq.push(["trackVisibleContentImpressions"]);' . $expectedTracker,
        ];
    }

    /**
     * @test
     */
    public function getTrackingCodeReturnsCodeWithDispatchedBeforeTrackPageViewEventCorrectly(): void
    {
        $beforeTrackPageViewEvent = new BeforeTrackPageViewEvent();
        $beforeTrackPageViewEvent->addJavaScriptCode('/* some code */');
        $beforeTrackPageViewEvent->addMatomoMethodCall('someMethodCall');

        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturnOnConsecutiveCalls(
                $beforeTrackPageViewEvent,
                new EnrichTrackPageViewEvent(),
                new AfterTrackPageViewEvent(),
            );

        $this->subject->setConfiguration(
            Configuration::createFromSiteConfiguration([
                'matomoIntegrationUrl' => 'https://www.example.net/',
                'matomoIntegrationSiteId' => 123,
            ])
        );

        self::assertStringContainsString('/* some code */_paq.push(["someMethodCall"]);_paq.push(["trackPageView"]);', $this->subject->getTrackingCode());
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
        $enrichTrackPageViewEvent = new EnrichTrackPageViewEvent();
        $enrichTrackPageViewEvent->setPageTitle($pageTitle);
        foreach ($customDimensions as $customDimension) {
            $enrichTrackPageViewEvent->addCustomDimension(...$customDimension);
        }

        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturnOnConsecutiveCalls(
                new BeforeTrackPageViewEvent(),
                $enrichTrackPageViewEvent,
                new AfterTrackPageViewEvent(),
            );

        $this->subject->setConfiguration(
            Configuration::createFromSiteConfiguration([
                'matomoIntegrationUrl' => 'https://www.example.net/',
                'matomoIntegrationSiteId' => 123,
            ])
        );

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
            '_paq.push(["trackPageView","some \"page title\""]);',
        ];

        yield 'with custom dimension which has double quotes in value' => [
            '',
            [[1, 'some "custom dimension" value']],
            '_paq.push(["trackPageView","",{"dimension1":"some \"custom dimension\" value"}]);',
        ];
    }

    /**
     * @test
     */
    public function getTrackingCodeReturnsCodeWithDispatchedAfterTrackPageViewEventCorrectly(): void
    {
        $afterTrackPageViewEvent = new AfterTrackPageViewEvent();
        $afterTrackPageViewEvent->addJavaScriptCode('/* some code */');
        $afterTrackPageViewEvent->addMatomoMethodCall('someMethodCall');

        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturnOnConsecutiveCalls(
                new BeforeTrackPageViewEvent(),
                new EnrichTrackPageViewEvent(),
                $afterTrackPageViewEvent,
            );

        $this->subject->setConfiguration(
            Configuration::createFromSiteConfiguration([
                'matomoIntegrationUrl' => 'https://www.example.net/',
                'matomoIntegrationSiteId' => 123,
            ])
        );

        self::assertStringContainsString('_paq.push(["trackPageView"]);/* some code */_paq.push(["someMethodCall"]);', $this->subject->getTrackingCode());
    }
}
