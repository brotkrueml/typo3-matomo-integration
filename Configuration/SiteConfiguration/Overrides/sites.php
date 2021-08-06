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
    // https://developer.matomo.org/guides/tracking-javascript-guide
    // https://developer.matomo.org/api-reference/tracking-javascript
//    'matomoIntegrationFeatures' => [
//        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':features',
//        'config' => [
//            'type' => 'select',
//            'renderType' => 'selectCheckBox',
//            'items' => [
//                [
//                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':features.disablePerformanceTracking',
//                    'disablePerformanceTracking',
//                ],
//                [
//                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':features.enableCrossDomainLinking',
//                    'enableCrossDomainLinking',
//                ],
//                [
//                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':features.logAllContentBlocksOnPage',
//                    'logAllContentBlocksOnPage',
//                ],
//            ],
//        ],
//    ],
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
            'default' => Brotkrueml\MatomoIntegration\Domain\Dto\Configuration::HEART_BEAT_TIMER_DEFAULT_ACTIVE_TIME_IN_SECONDS,
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
//    'matomoIntegrationEnableCrossDomainLinking' => [
//        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':features.enableCrossDomainLinking',
//        'config' => [
//            'type' => 'check',
//            'renderType' => 'checkboxToggle',
//            'items' => [[0 => '', 1 => '']],
//        ],
//    ],
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
            matomoIntegrationHeartBeatTimer, matomoIntegrationHeartBeatTimerActiveTimeInSeconds,
            matomoIntegrationTrackAllContentImpressions,
            matomoIntegrationTrackVisibleContentImpressions,
        ',
    ],
];
