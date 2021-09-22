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
        self::assertFalse($subject->cookieTracking);
        self::assertFalse($subject->heartBeatTimer);
        self::assertFalse($subject->linkTracking);
        self::assertFalse($subject->performanceTracking);
        self::assertFalse($subject->doNotTrack);
        self::assertFalse($subject->trackAllContentImpressions);
        self::assertFalse($subject->trackErrorPages);
        self::assertFalse($subject->trackVisibleContentImpressions);
        self::assertSame('', $subject->tagManagerContainerId);
        self::assertFalse($subject->tagManagerDebugMode);
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
    public function createFromSiteConfigurationWithUrlWithoutTrailingSlashGivenSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://example.org',
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
    public function createFromSiteConfigurationWithNoScriptEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'noScript',
        ]);

        self::assertTrue($subject->noScript);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithCookiesTrackingEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'cookieTracking',
        ]);

        self::assertTrue($subject->cookieTracking);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithDoNotTrackEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'doNotTrack',
        ]);

        self::assertTrue($subject->doNotTrack);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithHeartBeatTimerEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'heartBeatTimer',
        ]);

        self::assertTrue($subject->heartBeatTimer);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithLinkTrackingEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'linkTracking',
        ]);

        self::assertTrue($subject->linkTracking);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithPerformanceTrackingEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'performanceTracking',
        ]);

        self::assertTrue($subject->performanceTracking);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithTrackAllContentImpressionsEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'trackAllContentImpressions',
        ]);

        self::assertTrue($subject->trackAllContentImpressions);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithTrackVisibleContentImpressionsEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'trackVisibleContentImpressions',
        ]);

        self::assertTrue($subject->trackVisibleContentImpressions);
    }
    /**
     * @test
     */
    public function createFromSiteConfigurationWithTrackErrorPagesEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'trackErrorPages',
        ]);

        self::assertTrue($subject->trackErrorPages);
    }


    /**
     * @test
     */
    public function createFromSiteConfigurationWithMoreThanOneOptionEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'linkTracking,performanceTracking',
        ]);

        self::assertTrue($subject->linkTracking);
        self::assertTrue($subject->performanceTracking);
    }

    /**
     * @test
     */
    public function createFromSiteConfigurationWithMoreInvalidOptionIsIgnored(): void
    {
        $this->expectNotToPerformAssertions();

        Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'invalid',
        ]);
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
    public function createFromSiteConfigurationWithTagManagerDebugModeEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationTagManagerDebugMode' => true,
        ]);

        self::assertTrue($subject->tagManagerDebugMode);
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
