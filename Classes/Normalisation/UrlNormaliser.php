<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Normalisation;

/**
 * @internal
 * @todo If property access hooks are merged into PHP (8.4?), the normalisation can be
 *       done with a property accessor directly in Configuration object and this class
 *       gets obsolete then.
 * @see https://wiki.php.net/rfc/property-hooks
 */
final class UrlNormaliser
{
    public static function normalise(string $url): string
    {
        return \rtrim($url, '/') . '/';
    }
}
