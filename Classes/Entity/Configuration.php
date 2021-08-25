<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Entity;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
final class Configuration
{
    private const SITE_CONFIGURATION_PREFIX = 'matomoIntegration';

    /** @readonly */
    public string $url = '';
    /** @readonly */
    public int $siteId = 0;
    /** @readonly */
    public bool $noScript = false;
    /** @readonly */
    public bool $heartBeatTimer = false;
    /** @readonly */
    public bool $linkTracking = false;
    /** @readonly */
    public bool $performanceTracking = false;
    /** @readonly */
    public bool $trackAllContentImpressions = false;
    /** @readonly */
    public bool $trackVisibleContentImpressions = false;
    /** @readonly */
    public string $tagManagerContainerId = '';
    /** @readonly */
    public bool $tagManagerDebugMode = false;

    private function __construct()
    {
    }

    /**
     * @param array<string,bool|int|string> $siteConfiguration
     */
    public static function createFromSiteConfiguration(array $siteConfiguration): self
    {
        $configuration = new self();
        foreach ($siteConfiguration as $name => $value) {
            if (!\str_starts_with($name, self::SITE_CONFIGURATION_PREFIX)) {
                continue;
            }

            $property = \lcfirst(\substr($name, \strlen(self::SITE_CONFIGURATION_PREFIX)));
            if ($property === 'options') {
                $options = GeneralUtility::trimExplode(',', (string)$value, true);
                foreach ($options as $option) {
                    self::setConfiguration($configuration, $option, true);
                }
                continue;
            }
            if (!\property_exists(self::class, $property)) {
                continue;
            }
            self::setConfiguration($configuration, $property, $value);
        }

        return $configuration;
    }

    /**
     * @param bool|int|string $value
     */
    private static function setConfiguration(Configuration $configuration, string $property, $value): void
    {
        try {
            // @phpstan-ignore-next-line
            $type = (new \ReflectionProperty(self::class, $property))->getType()->getName();
            if ($type === 'string') {
                $configuration->$property = (string)$value;
            } elseif ($type === 'int') {
                $configuration->$property = (int)$value;
            } elseif ($type === 'bool') {
                $configuration->$property = (bool)$value;
            }
        } catch (\ReflectionException $e) {
        }
    }
}
