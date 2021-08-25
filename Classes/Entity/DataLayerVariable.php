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
use Brotkrueml\MatomoIntegration\Exceptions\InvalidDataLayerVariableValue;

/**
 * @internal
 */
final class DataLayerVariable
{
    private string $name;
    /** @var string|int|float|JavaScriptCode */
    private $value;

    /**
     * @param string|int|float|JavaScriptCode $value
     */
    public function __construct(string $name, $value)
    {
        $this->checkValue($value);
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @param mixed $value
     */
    private function checkValue($value): void
    {
        if (\is_string($value)) {
            return;
        }

        if (\is_int($value)) {
            return;
        }

        if (\is_float($value)) {
            return;
        }

        if ($value instanceof JavaScriptCode) {
            return;
        }

        throw new InvalidDataLayerVariableValue(
            \sprintf(
                'A data layer value with the invalid type "%s" was given, allowed: int, float, string, %s',
                \get_debug_type($value),
                JavaScriptCode::class
            ),
            1629652718
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|int|float|JavaScriptCode
     */
    public function getValue()
    {
        return $this->value;
    }
}
