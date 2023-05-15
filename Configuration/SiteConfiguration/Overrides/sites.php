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
            'eval' => 'trim',
        ],
    ],
    'matomoIntegrationOptions' => [
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':options',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectCheckBox',
            'items' => [
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':noScript',
                    'value' => 'noScript',
                ],
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':requireCookieConsent',
                    'value' => 'requireCookieConsent',
                ],
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':cookieTracking',
                    'value' => 'cookieTracking',
                ],
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':linkTracking',
                    'value' => 'linkTracking',
                ],
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':performanceTracking',
                    'value' => 'performanceTracking',
                ],
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':heartBeatTimer',
                    'value' => 'heartBeatTimer',
                ],
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':disableBrowserFeatureDetection',
                    'value' => 'disableBrowserFeatureDetection',
                ],
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':doNotTrack',
                    'value' => 'doNotTrack',
                ],
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':trackAllContentImpressions',
                    'value' => 'trackAllContentImpressions',
                ],
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':trackVisibleContentImpressions',
                    'value' => 'trackVisibleContentImpressions',
                ],
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':trackErrorPages',
                    'value' => 'trackErrorPages',
                ],
                [
                    'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':trackJavaScriptErrors',
                    'value' => 'trackJavaScriptErrors',
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
        'label' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':tagManagerContainerIds',
        'description' => Brotkrueml\MatomoIntegration\Extension::LANGUAGE_PATH_SITECONF . ':tagManagerContainerIds.description',
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
            'items' => [[
                'label' => '',
                'value' => '',
            ]],
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

if ((new TYPO3\CMS\Core\Information\Typo3Version())->getMajorVersion() < 12) {
    foreach ($GLOBALS['SiteConfiguration']['site']['columns']['matomoIntegrationOptions']['config']['items'] as &$item) {
        $item[0] = $item['label'];
        $item[1] = $item['value'];
        unset($item['label']);
        unset($item['value']);
    }

    $GLOBALS['SiteConfiguration']['site']['columns']['matomoIntegrationTagManagerDebugMode']['config']['items'][0][0]
        = $GLOBALS['SiteConfiguration']['site']['columns']['matomoIntegrationTagManagerDebugMode']['config']['items'][0]['label'];
    $GLOBALS['SiteConfiguration']['site']['columns']['matomoIntegrationTagManagerDebugMode']['config']['items'][0][1]
        = $GLOBALS['SiteConfiguration']['site']['columns']['matomoIntegrationTagManagerDebugMode']['config']['items'][0]['value'];
    unset($GLOBALS['SiteConfiguration']['site']['columns']['matomoIntegrationTagManagerDebugMode']['config']['items'][0]['label']);
    unset($GLOBALS['SiteConfiguration']['site']['columns']['matomoIntegrationTagManagerDebugMode']['config']['items'][0]['value']);
}
