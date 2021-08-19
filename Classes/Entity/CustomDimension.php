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
final class CustomDimension
{
    private int $id;
    private string $value;

    public function __construct(int $id, string $value)
    {
        $this->ensureIdIsValid($id);
        $this->id = $id;
        $this->value = $value;
    }

    private function ensureIdIsValid(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidCustomDimensionId(
                \sprintf('The id for a custom dimension has to be a positive integer, "%d" given', $id),
                1628782795
            );
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
