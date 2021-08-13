<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\TrackingCode;

use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Entity\CustomDimension;
use Brotkrueml\MatomoIntegration\Event\AfterTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent;
use Brotkrueml\MatomoIntegration\TrackingCode\JavaScriptTrackingCodeBuilder;
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

        $expectedTracker = '(function(){var u="https://www.example.net/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",123]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();';

        yield 'Minimum configuration' => [
            $defaultConfiguration,
            'var _paq=window._paq||[];_paq.push(["trackPageView"]);' . $expectedTracker
        ];

        yield 'With link tracking enabled' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationLinkTracking' => true]),
            'var _paq=window._paq||[];_paq.push(["trackPageView"]);_paq.push(["enableLinkTracking"]);' . $expectedTracker
        ];

        yield 'With performance tracking disabled' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationPerformanceTracking' => false]),
            'var _paq=window._paq||[];_paq.push(["trackPageView"]);_paq.push(["disablePerformanceTracking"]);' . $expectedTracker
        ];

        yield 'With heart beat timer enabled' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationHeartBeatTimer' => true]),
            'var _paq=window._paq||[];_paq.push(["trackPageView"]);_paq.push(["enableHeartBeatTimer"]);' . $expectedTracker
        ];

        yield 'With heart beat timer enabled and active time is 0' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationHeartBeatTimer' => true], ['matomoIntegrationHeartBeatTimerActiveTimeInSeconds' => 0]),
            'var _paq=window._paq||[];_paq.push(["trackPageView"]);_paq.push(["enableHeartBeatTimer"]);' . $expectedTracker
        ];

        yield 'With heart beat timer enabled and active time is default value' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationHeartBeatTimer' => true], ['matomoIntegrationHeartBeatTimerActiveTimeInSeconds' => Configuration::HEART_BEAT_TIMER_DEFAULT_ACTIVE_TIME_IN_SECONDS]),
            'var _paq=window._paq||[];_paq.push(["trackPageView"]);_paq.push(["enableHeartBeatTimer"]);' . $expectedTracker
        ];

        yield 'With heart beat timer enabled and active time is defined' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationHeartBeatTimer' => true], ['matomoIntegrationHeartBeatTimerActiveTimeInSeconds' => 42]),
            'var _paq=window._paq||[];_paq.push(["trackPageView"]);_paq.push(["enableHeartBeatTimer", 42]);' . $expectedTracker
        ];

        yield 'With track all content impressions enabled' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationTrackAllContentImpressions' => true]),
            'var _paq=window._paq||[];_paq.push(["trackPageView"]);_paq.push(["trackAllContentImpressions"]);' . $expectedTracker
        ];

        yield 'With track visible content impressions enabled' => [
            \array_merge($defaultConfiguration, ['matomoIntegrationTrackVisibleContentImpressions' => true]),
            'var _paq=window._paq||[];_paq.push(["trackPageView"]);_paq.push(["trackVisibleContentImpressions"]);' . $expectedTracker
        ];
    }

    /**
     * @test
     */
    public function getTrackingCodeReturnsCodeWithDispatchedBeforeTrackPageViewEventCorrectly(): void
    {
        $beforeTrackPageViewEventevent = new BeforeTrackPageViewEvent();
        $beforeTrackPageViewEventevent->addCode('/* some code */');

        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturnOnConsecutiveCalls(
                $beforeTrackPageViewEventevent,
                new EnrichTrackPageViewEvent(),
                new AfterTrackPageViewEvent(),
            );

        $this->subject->setConfiguration(
            Configuration::createFromSiteConfiguration([
                'matomoIntegrationUrl' => 'https://www.example.net/',
                'matomoIntegrationSiteId' => 123,
            ])
        );

        self::assertStringContainsString('/* some code */_paq.push(["trackPageView"]);', $this->subject->getTrackingCode());
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
            $enrichTrackPageViewEvent->addCustomDimension($customDimension);
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
            [new CustomDimension(1, 'some custom dimension value')],
            '_paq.push(["trackPageView","some page title",{"dimension1":"some custom dimension value"}]);',
        ];

        yield 'with page title and with two custom dimensions' => [
            'some page title',
            [
                new CustomDimension(1, 'some custom dimension value'),
                new CustomDimension(2, 'another custom dimension value')
            ],
            '_paq.push(["trackPageView","some page title",{"dimension1":"some custom dimension value","dimension2":"another custom dimension value"}]);',
        ];

        yield 'without page title and with one custom dimension' => [
            '',
            [new CustomDimension(1, 'some custom dimension value')],
            '_paq.push(["trackPageView","",{"dimension1":"some custom dimension value"}]);',
        ];

        yield 'with page title which has double quotes' => [
            'some "page title"',
            [],
            '_paq.push(["trackPageView","some \"page title\""]);',
        ];

        yield 'with custom dimension which has double quotes in value' => [
            '',
            [new CustomDimension(1, 'some "custom dimension" value')],
            '_paq.push(["trackPageView","",{"dimension1":"some \"custom dimension\" value"}]);',
        ];
    }

    /**
     * @test
     */
    public function getTrackingCodeReturnsCodeWithDispatchedAfterTrackPageViewEventCorrectly(): void
    {
        $afterTrackPageViewEvent = new AfterTrackPageViewEvent();
        $afterTrackPageViewEvent->addCode('/* some code */');

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

        self::assertStringContainsString('_paq.push(["trackPageView"]);/* some code */', $this->subject->getTrackingCode());
    }
}
