<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration;

/**
 * @internal
 */
final class Extension
{
    public const KEY = 'matomo_integration';

    private const LANGUAGE_PATH = 'LLL:EXT:' . self::KEY . '/Resources/Private/Language/';
    public const LANGUAGE_PATH_SITECONF = self::LANGUAGE_PATH . 'SiteConfiguration.xlf';

    public const DEFAULT_TEMPLATE_ERROR_PAGES = '{statusCode}/URL = {path} /From = {referrer}';
}
