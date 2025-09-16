<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Code;

use Brotkrueml\MatomoIntegration\Exceptions\InvalidMatomoMethodName;

/**
 * @internal
 */
final class MatomoMethodCall implements \Stringable
{
    /**
     * @see https://regex101.com/r/vxhiIw/1
     */
    private const METHOD_NAME_REGEX = '/^[a-z]+$/i';

    private readonly string $methodName;
    /**
     * @var list<array|bool|int|string|JavaScriptCode>
     */
    private readonly array $parameters;

    public function __construct(string $methodName, array|bool|int|float|string|JavaScriptCode ...$parameters)
    {
        $this->checkMethodName($methodName);
        $this->methodName = $methodName;
        $this->parameters = $parameters;
    }

    private function checkMethodName(string $methodName): void
    {
        if (! \preg_match(self::METHOD_NAME_REGEX, $methodName)) {
            throw new InvalidMatomoMethodName(
                \sprintf(
                    'The given Matomo method name "%s" is not valid, only characters between a and z are allowed!',
                    $methodName,
                ),
                1629212630,
            );
        }
    }

    public function __toString(): string
    {
        $pushArguments = [$this->formatArgument($this->methodName)];
        foreach ($this->parameters as $argument) {
            $pushArguments[] = $this->formatArgument($argument);
        }

        return '_paq.push([' . \implode(',', $pushArguments) . ']);';
    }

    /**
     * @param list<mixed>|bool|int|float|string|JavaScriptCode $value
     */
    private function formatArgument(array|bool|int|float|string|JavaScriptCode $value): string
    {
        if (\is_string($value)) {
            return $this->convertStringValue($value);
        }

        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (\is_array($value)) {
            return $this->convertArrayValue($value);
        }

        return (string) $value;
    }

    private function convertStringValue(string $value): string
    {
        try {
            $value = \json_decode($value, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            // No JSON string, just a normal string
        }

        return \json_encode($value, \JSON_HEX_AMP | \JSON_HEX_APOS | \JSON_HEX_QUOT | \JSON_HEX_TAG) ?: '""';
    }

    /**
     * @param list<mixed> $value
     */
    private function convertArrayValue(array $value): string
    {
        $formattedArray = [];
        foreach ($value as $singleValue) {
            $formattedArray[] = $this->formatArgument($singleValue);
        }

        return '[' . \implode(',', $formattedArray) . ']';
    }
}
