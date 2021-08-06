<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Domain\TrackingCode;

use Brotkrueml\MatomoIntegration\Domain\Dto\Configuration;
use Brotkrueml\MatomoIntegration\Domain\TrackingCode\JavaScriptTrackingCodeBuilder;
use PHPUnit\Framework\TestCase;

final class JavaScriptTrackingCodeBuilderTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataProviderForGetTrackingCode
     */
    public function getTrackingCodeReturnsTrackingCodeCorrectly(array $configuration, string $expected): void
    {
        $subject = new JavaScriptTrackingCodeBuilder(
            Configuration::createFromSiteConfiguration($configuration)
        );

        self::assertSame($expected, $subject->getTrackingCode());
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
}
