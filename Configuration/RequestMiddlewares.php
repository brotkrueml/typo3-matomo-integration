<?php

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

return [
    'frontend' => [
        'brotkrueml/matomo-integration/tracking-code-injection' => [
            'target' => Brotkrueml\MatomoIntegration\Middleware\TrackingCodeInjection::class,
            'before' => [
                'typo3/cms-frontend/content-length-headers',
            ],
        ],
    ],
];
