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
    'matomoIntegrationOptions' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':options',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectCheckBox',
            'items' => [
                [
                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':noScript',
                    'noScript',
                ],
                [
                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':cookieTracking',
                    'cookieTracking',
                ],
                [
                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':linkTracking',
                    'linkTracking',
                ],
                [
                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':performanceTracking',
                    'performanceTracking',
                ],
                [
                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':heartBeatTimer',
                    'heartBeatTimer',
                ],
                [
                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':doNotTrack',
                    'doNotTrack',
                ],
                [
                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':trackAllContentImpressions',
                    'trackAllContentImpressions',
                ],
                [
                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':trackVisibleContentImpressions',
                    'trackVisibleContentImpressions',
                ],
                [
                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':trackErrorPages',
                    'trackErrorPages',
                ],
                [
                    Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':trackJavaScriptErrors',
                    'trackJavaScriptErrors',
                ],
            ],
            'default' => 'cookieTracking,linkTracking,performanceTracking',
        ],
    ],
    'matomoIntegrationErrorPagesTemplate' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':errorPagesTemplate',
        'config' => [
            'type' => 'input',
            'eval' => 'trim',
            'default' => Brotkrueml\MatomoIntegration\Extension::DEFAULT_TEMPLATE_ERROR_PAGES,
        ],
    ],
    'matomoIntegrationTagManagerContainerId' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':tagManagerContainerId',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
        ],
    ],
    'matomoIntegrationTagManagerDebugMode' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':tagManagerDebugMode',
        'config' => [
            'type' => 'check',
            'renderType' => 'checkboxToggle',
            'items' => [[0 => '', 1 => '']],
        ],
    ],
];

$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ',
    --div--;' . Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':tabName,
    --palette--;;matomoIntegrationInstallation,
    --palette--;;matomoIntegrationOptions,
    --palette--;;matomoIntegrationTagManager,
';

$GLOBALS['SiteConfiguration']['site']['palettes'] += [
    'matomoIntegrationInstallation' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':installation',
        'showitem' => 'matomoIntegrationUrl, matomoIntegrationSiteId',
    ],
    'matomoIntegrationOptions' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':options',
        'showitem' => 'matomoIntegrationOptions, --linebreak--, matomoIntegrationErrorPagesTemplate',
    ],
    'matomoIntegrationTagManager' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':tagManager',
        'showitem' => 'matomoIntegrationTagManagerContainerId, matomoIntegrationTagManagerDebugMode',
    ],
];
