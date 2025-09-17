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
final readonly class DataLayerVariable
{
    public function __construct(
        public string $name,
        public string|int|float|JavaScriptCode $value,
    ) {}
}
