<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Entity;

use Brotkrueml\MatomoIntegration\Exceptions\InvalidConfigurationOption;
use Brotkrueml\MatomoIntegration\Normalisation\UrlNormaliser;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
final class Configuration
{
    private const SITE_CONFIGURATION_PREFIX = 'matomoIntegration';

    public string $url = '';
    public int $siteId = 0;
    public bool $noScript = false;
    public bool $cookieTracking = false;
    public bool $disableBrowserFeatureDetection = false;
    public bool $disableCampaignParameters = false;
    public bool $doNotTrack = false;
    public string $errorPagesTemplate = '';
    public bool $fileTracking = false;
    public bool $heartBeatTimer = false;
    public bool $linkTracking = false;
    public bool $performanceTracking = false;
    public bool $requireConsent = false;
    public bool $requireCookieConsent = false;
    public bool $trackAllContentImpressions = false;
    public bool $trackErrorPages = false;
    public bool $trackJavaScriptErrors = false;
    public bool $trackVisibleContentImpressions = false;
    /**
     * @var list<string>
     */
    public array $tagManagerContainerIds = [];
    public bool $tagManagerDebugMode = false;

    private function __construct() {}

    /**
     * @param array<string,bool|int|string> $siteConfiguration
     */
    public static function createFromSiteConfiguration(array $siteConfiguration): self
    {
        $configuration = new self();
        foreach ($siteConfiguration as $name => $value) {
            if (! \str_starts_with($name, self::SITE_CONFIGURATION_PREFIX)) {
                continue;
            }

            self::processConfiguration($configuration, $name, $value);
        }

        return $configuration;
    }

    /**
     * @param bool|int|string $value
     */
    private static function processConfiguration(self $configuration, string $name, $value): void
    {
        $property = self::mapConfigurationNameToClassProperty($name);
        if ($property === 'options') {
            self::processOptions($configuration, (string) $value);
            return;
        }

        if (! \property_exists(self::class, $property)) {
            return;
        }

        if ($property === 'url') {
            $value = UrlNormaliser::normalise((string) $value);
        }

        self::setConfiguration($configuration, $property, $value);
    }

    private static function mapConfigurationNameToClassProperty(string $name): string
    {
        return \lcfirst(\substr($name, \strlen(self::SITE_CONFIGURATION_PREFIX)));
    }

    private static function processOptions(self $configuration, string $commaDelimitedOptions): void
    {
        $options = GeneralUtility::trimExplode(',', $commaDelimitedOptions, true);
        foreach ($options as $option) {
            if (! \property_exists($configuration, $option)) {
                throw new InvalidConfigurationOption(
                    \sprintf(
                        'The Matomo integration option "%s" is not valid! Please check your site configuration.',
                        $option,
                    ),
                    1760370369,
                );
            }

            self::setConfiguration($configuration, $option, true);
        }
    }

    /**
     * @param bool|int|string $value
     */
    private static function setConfiguration(self $configuration, string $property, $value): void
    {
        $type = self::getTypeForProperty($property);
        $configuration->{$property} = match ($type) {
            'string' => (string) $value,
            'int' => (int) $value,
            'bool' => (bool) $value,
            'array' => \is_string($value) ? GeneralUtility::trimExplode(',', $value, true) : $value,
            // Happify phpstan, this case should not happen as the properties passed to this method are guarded by the calling code
            default => throw new \UnhandledMatchError($type),
        };
    }

    private static function getTypeForProperty(string $property): string
    {
        try {
            $type = (new \ReflectionProperty(self::class, $property))->getType();
            if (! $type instanceof \ReflectionNamedType) {
                return '';
            }
            if (! $type->isBuiltin()) {
                return '';
            }

            return $type->getName();
        } catch (\ReflectionException) {
        }

        return '';
    }
}
