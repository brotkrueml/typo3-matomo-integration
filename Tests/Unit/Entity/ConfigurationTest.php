<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Entity;

use Brotkrueml\MatomoIntegration\Entity\Configuration;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function createFromSiteConfigurationWithEmptyArrayReturnsInstanceWithDefaultValues(): void
    {
        $subject = Configuration::createFromSiteConfiguration([]);

        self::assertSame('', $subject->url);
        self::assertSame(0, $subject->siteId);
        self::assertFalse($subject->noScript);
        self::assertFalse($subject->heartBeatTimer);
        self::assertSame(Configuration::HEART_BEAT_TIMER_DEFAULT_ACTIVE_TIME_IN_SECONDS, $subject->heartBeatTimerActiveTimeInSeconds);
        self::assertFalse($subject->linkTracking);
        self::assertTrue($subject->performanceTracking);
        self::assertFalse($subject->trackAllContentImpressions);
        self::assertFalse($subject->trackVisibleContentImpressions);
        self::assertSame('', $subject->tagManagerContainerId);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithUrlGivenSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://example.org/',
        ]);

        self::assertSame('https://example.org/', $subject->url);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithSiteIdAsIntGivenSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationSiteId' => 42,
        ]);

        self::assertSame(42, $subject->siteId);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithNoScriptDefinedAsTrueSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationNoScript' => true,
        ]);

        self::assertTrue($subject->noScript);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithHeartBeatTimerDefinedAsTrueSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationHeartBeatTimer' => true,
        ]);

        self::assertTrue($subject->heartBeatTimer);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithHeartBeatTimerActiveTimeInSecondsGivenSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationHeartBeatTimerActiveTimeInSeconds' => 25,
        ]);

        self::assertSame(25, $subject->heartBeatTimerActiveTimeInSeconds);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithLinkTrackingDefinedAsTrueSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationLinkTracking' => true,
        ]);

        self::assertTrue($subject->linkTracking);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithPerformanceTrackingDefinedAsFalseSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationPerformanceTracking' => false,
        ]);

        self::assertFalse($subject->performanceTracking);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithTrackAllContentImpressionsDefinedAsTrueSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationTrackAllContentImpressions' => true,
        ]);

        self::assertTrue($subject->trackAllContentImpressions);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithTrackVisibleContentImpressionsDefinedAsTrueSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationTrackVisibleContentImpressions' => true,
        ]);

        self::assertTrue($subject->trackVisibleContentImpressions);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithTagManagerContainerIdGivenSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationTagManagerContainerId' => 'someId',
        ]);

        self::assertSame('someId', $subject->tagManagerContainerId);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithExpectedIntegerGivenAsStringSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationSiteId' => '42',
        ]);

        self::assertSame(42, $subject->siteId);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithExpectedBooleanGivenAsIntegerSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationNoScript' => 1,
        ]);

        self::assertTrue($subject->noScript);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationANonMatomoIntegrationSettingsIsDiscarded(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'someOtherSettings' => 'some value',
        ]);

        self::assertSame('', $subject->url);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationANonExistingMatomoIntegrationSettingsIsDiscarded(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationNonExisting' => 'some value',
        ]);

        self::assertSame('', $subject->url);
    }
}
