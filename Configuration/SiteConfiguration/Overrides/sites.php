<?php

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

$GLOBALS['SiteConfiguration']['site']['columns'] += [
    'matomoIntegrationUrl' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':url',
        'config' => [
            'type' => 'input',
            'eval' => 'trim',
        ],
    ],
    'matomoIntegrationSiteId' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':siteId',
        'config' => [
            'type' => 'input',
            'size' => 10,
            'eval' => 'int',
        ],
    ],
    'matomoIntegrationNoScript' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':noScript',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [[0 => '', 1 => '']],
            'default' => 1,
        ],
    ],
    'matomoIntegrationLinkTracking' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':linkTracking',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [[0 => '', 1 => '']],
            'default' => 1,
        ],
    ],
    'matomoIntegrationHeartBeatTimer' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':heartBeatTimer',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [[0 => '', 1 => '']],
        ],
    ],
    'matomoIntegrationHeartBeatTimerActiveTimeInSeconds' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':heartBeatTimerActiveTimeInSeconds',
        'description' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':heartBeatTimerActiveTimeInSeconds.description',
        'config' => [
            'type' => 'input',
            'size' => 10,
            'eval' => 'int',
            'default' => Brotkrueml\MatomoIntegration\Entity\Configuration::HEART_BEAT_TIMER_DEFAULT_ACTIVE_TIME_IN_SECONDS,
        ],
    ],
    'matomoIntegrationTrackAllContentImpressions' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':trackAllContentImpressions',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [[0 => '', 1 => '']],
        ],
    ],
    'matomoIntegrationTrackVisibleContentImpressions' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':trackVisibleContentImpressions',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [[0 => '', 1 => '']],
        ],
    ],
    'matomoIntegrationPerformanceTracking' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':performanceTracking',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [[0 => '', 1 => '']],
            'default' => 1,
        ],
    ],
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ',
    --div--;' . Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':tabName,
    --palette--;;matomoIntegrationInstallation,
    --palette--;;matomoIntegrationOptions,
';

$GLOBALS['SiteConfiguration']['site']['palettes'] += [
    'matomoIntegrationInstallation' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':installation',
        'showitem' => 'matomoIntegrationUrl, matomoIntegrationSiteId,matomoIntegrationNoScript',
    ],
    'matomoIntegrationOptions' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':options',
        'showitem' => '
            matomoIntegrationLinkTracking,
            matomoIntegrationPerformanceTracking,
            matomoIntegrationHeartBeatTimer,
            matomoIntegrationHeartBeatTimerActiveTimeInSeconds,
            --linebreak--,
            matomoIntegrationTrackAllContentImpressions,
            matomoIntegrationTrackVisibleContentImpressions,
        ',
    ],
];
