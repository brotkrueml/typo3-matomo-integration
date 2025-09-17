<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Entity;

use Brotkrueml\MatomoIntegration\Exceptions\InvalidCustomDimensionId;

/**
 * @internal
 */
final readonly class CustomDimension
{
    public function __construct(
        public int $id,
        public string $value,
    ) {
        $this->ensureIdIsValid();
    }

    private function ensureIdIsValid(): void
    {
        if ($this->id <= 0) {
            throw new InvalidCustomDimensionId(
                \sprintf('The id for a custom dimension has to be a positive integer, "%d" given', $this->id),
                1628782795,
            );
        }
    }
}
