<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\JavaScript;

use Brotkrueml\MatomoIntegration\Code\JavaScriptCode;

/**
 * @internal
 */
final class JavaScriptObjectPairCollector implements \Stringable
{
    /** @var array<string, string|int|float|JavaScriptCode> */
    private array $pairs = [];

    /**
     * @param string|int|float|JavaScriptCode $value
     */
    public function addPair(string $name, $value): void
    {
        $this->pairs[$name] = \sprintf(
            '"%s":%s',
            \addcslashes($name, '"'),
            \is_string($value) ? \sprintf('"%s"', \addcslashes($value, '"')) : $value
        );
    }

    public function __toString(): string
    {
        return \sprintf('{%s}', \implode(',', $this->pairs));
    }
}
