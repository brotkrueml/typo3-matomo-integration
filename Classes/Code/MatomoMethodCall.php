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
use Brotkrueml\MatomoIntegration\Exceptions\InvalidMatomoMethodParameter;

/**
 * @internal
 */
final class MatomoMethodCall implements \Stringable
{
    /**
     * @see https://regex101.com/r/vxhiIw/1
     */
    private const METHOD_NAME_REGEX = '/^[a-z]+$/i';
    private string $methodName;
    /**
     * @var list<array|bool|int|string|JavaScriptCode>
     */
    private array $parameters;

    /**
     * @param array|bool|int|string|JavaScriptCode ...$parameters
     */
    public function __construct(string $methodName, ...$parameters)
    {
        $this->checkMethodName($methodName);
        $this->methodName = $methodName;
        $this->parameters = $parameters;
    }

    private function checkMethodName(string $methodName): void
    {
        if (! preg_match(self::METHOD_NAME_REGEX, $methodName)) {
            throw new InvalidMatomoMethodName(
                \sprintf(
                    'The given Matomo method name "%s" is not valid, only characters between a and z are allowed!',
                    $methodName
                ),
                1629212630
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
     * @param mixed $value
     * @return int|string
     */
    private function formatArgument($value)
    {
        if (\is_int($value)) {
            return $value;
        }

        if (\is_string($value)) {
            return $this->convertStringValue($value);
        }

        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (\is_array($value)) {
            return $this->convertArrayValue($value);
        }

        if ($value instanceof JavaScriptCode) {
            return (string)$value;
        }

        throw new InvalidMatomoMethodParameter(
            \sprintf(
                'A Matomo method argument with the invalid type "%s" was given, allowed: array, bool, int, string, %s',
                \get_debug_type($value),
                JavaScriptCode::class
            ),
            1629212630
        );
    }

    private function convertStringValue(string $value): string
    {
        try {
            \json_decode($value, false, 512, \JSON_THROW_ON_ERROR);
            return $value;
        } catch (\JsonException $e) {
            return '"' . \addcslashes($value, '"') . '"';
        }
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

        return '[' . implode(',', $formattedArray) . ']';
    }
}
