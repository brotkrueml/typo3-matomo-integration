<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Entity;

use Brotkrueml\MatomoIntegration\Code\JavaScriptCode;

/**
 * @internal
 */
final class DataLayerVariable
{
    public function __construct(
        public readonly string $name,
        public readonly string|int|float|JavaScriptCode $value,
    ) {}
}
