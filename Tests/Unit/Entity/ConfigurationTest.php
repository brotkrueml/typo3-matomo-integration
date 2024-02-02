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
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    #[Test]
    public function createFromSiteConfigurationWithEmptyArrayReturnsInstanceWithDefaultValues(): void
    {
        $subject = Configuration::createFromSiteConfiguration([]);

        self::assertSame('', $subject->url);
        self::assertSame(0, $subject->siteId);
        self::assertFalse($subject->noScript);
        self::assertFalse($subject->requireConsent);
        self::assertFalse($subject->requireCookieConsent);
        self::assertFalse($subject->cookieTracking);
        self::assertFalse($subject->disableBrowserFeatureDetection);
        self::assertSame('', $subject->errorPagesTemplate);
        self::assertFalse($subject->fileTracking);
        self::assertFalse($subject->heartBeatTimer);
        self::assertFalse($subject->linkTracking);
        self::assertFalse($subject->performanceTracking);
        self::assertFalse($subject->doNotTrack);
        self::assertFalse($subject->trackAllContentImpressions);
        self::assertFalse($subject->trackErrorPages);
        self::assertFalse($subject->trackJavaScriptErrors);
        self::assertFalse($subject->trackVisibleContentImpressions);
        self::assertSame([], $subject->tagManagerContainerIds);
        self::assertFalse($subject->tagManagerDebugMode);
    }

    #[Test]
    public function createFromSiteConfigurationWithUrlGivenSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://example.org/',
        ]);

        self::assertSame('https://example.org/', $subject->url);
    }

    #[Test]
    public function createFromSiteConfigurationWithUrlWithoutTrailingSlashGivenSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://example.org',
        ]);

        self::assertSame('https://example.org/', $subject->url);
    }

    #[Test]
    public function createFromSiteConfigurationWithSiteIdAsIntGivenSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationSiteId' => 42,
        ]);

        self::assertSame(42, $subject->siteId);
    }

    #[Test]
    public function createFromSiteConfigurationWithNoScriptEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'noScript',
        ]);

        self::assertTrue($subject->noScript);
    }

    #[Test]
    public function createFromSiteConfigurationWithRequireCookieConsentEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'requireCookieConsent',
        ]);

        self::assertTrue($subject->requireCookieConsent);
    }

    #[Test]
    public function createFromSiteConfigurationWithRequireConsentEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'requireConsent',
        ]);

        self::assertTrue($subject->requireConsent);
    }

    #[Test]
    public function createFromSiteConfigurationWithCookiesTrackingEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'cookieTracking',
        ]);

        self::assertTrue($subject->cookieTracking);
    }

    #[Test]
    public function createFromSiteConfigurationWithDisableBrowserFeatureDetectionSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'disableBrowserFeatureDetection',
        ]);

        self::assertTrue($subject->disableBrowserFeatureDetection);
    }

    #[Test]
    public function createFromSiteConfigurationWithDoNotTrackEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'doNotTrack',
        ]);

        self::assertTrue($subject->doNotTrack);
    }

    #[Test]
    public function createFromSiteConfigurationWithHeartBeatTimerEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'heartBeatTimer',
        ]);

        self::assertTrue($subject->heartBeatTimer);
    }

    #[Test]
    public function createFromSiteConfigurationWithFileTrackingEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'fileTracking',
        ]);

        self::assertTrue($subject->fileTracking);
    }

    #[Test]
    public function createFromSiteConfigurationWithLinkTrackingEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'linkTracking',
        ]);

        self::assertTrue($subject->linkTracking);
    }

    #[Test]
    public function createFromSiteConfigurationWithPerformanceTrackingEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'performanceTracking',
        ]);

        self::assertTrue($subject->performanceTracking);
    }

    #[Test]
    public function createFromSiteConfigurationWithTrackAllContentImpressionsEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'trackAllContentImpressions',
        ]);

        self::assertTrue($subject->trackAllContentImpressions);
    }

    #[Test]
    public function createFromSiteConfigurationWithTrackVisibleContentImpressionsEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'trackVisibleContentImpressions',
        ]);

        self::assertTrue($subject->trackVisibleContentImpressions);
    }

    #[Test]
    public function createFromSiteConfigurationWithTrackErrorPagesEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'trackErrorPages',
        ]);

        self::assertTrue($subject->trackErrorPages);
    }

    #[Test]
    public function createFromSiteConfigurationWithErrorPagesTemplateSetsInstanceValueCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationErrorPagesTemplate' => 'Some template',
        ]);

        self::assertSame('Some template', $subject->errorPagesTemplate);
    }

    #[Test]
    public function createFromSiteConfigurationWithTrackJavaScriptErrorsEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'trackJavaScriptErrors',
        ]);

        self::assertTrue($subject->trackJavaScriptErrors);
    }

    #[Test]
    public function createFromSiteConfigurationWithMoreThanOneOptionEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'linkTracking,performanceTracking',
        ]);

        self::assertTrue($subject->linkTracking);
        self::assertTrue($subject->performanceTracking);
    }

    #[Test]
    public function createFromSiteConfigurationWithMoreInvalidOptionIsIgnored(): void
    {
        $this->expectNotToPerformAssertions();

        Configuration::createFromSiteConfiguration([
            'matomoIntegrationOptions' => 'invalid',
        ]);
    }

    #[Test]
    public function createFromSiteConfigurationWithOneTagManagerContainerIdGivenSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationTagManagerContainerIds' => 'someId',
        ]);

        self::assertSame(['someId'], $subject->tagManagerContainerIds);
    }

    #[Test]
    public function createFromSiteConfigurationWithMultipleTagManagerContainerIdsGivenSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationTagManagerContainerIds' => 'someId, anotherId,, someMoreId,',
        ]);

        self::assertCount(3, $subject->tagManagerContainerIds);
        self::assertContains('someId', $subject->tagManagerContainerIds);
        self::assertContains('anotherId', $subject->tagManagerContainerIds);
        self::assertContains('someMoreId', $subject->tagManagerContainerIds);
    }

    #[Test]
    public function createFromSiteConfigurationWithTagManagerDebugModeEnabledSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationTagManagerDebugMode' => true,
        ]);

        self::assertTrue($subject->tagManagerDebugMode);
    }

    #[Test]
    public function createFromSiteConfigurationWithExpectedIntegerGivenAsStringSetsInstanceValuesCorrectly(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationSiteId' => '42',
        ]);

        self::assertSame(42, $subject->siteId);
    }

    #[Test]
    public function createFromSiteConfigurationANonMatomoIntegrationSettingsIsDiscarded(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'someOtherSettings' => 'some value',
        ]);

        self::assertSame('', $subject->url);
    }

    #[Test]
    public function createFromSiteConfigurationANonExistingMatomoIntegrationSettingsIsDiscarded(): void
    {
        $subject = Configuration::createFromSiteConfiguration([
            'matomoIntegrationNonExisting' => 'some value',
        ]);

        self::assertSame('', $subject->url);
    }
}
