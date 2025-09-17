<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Code;

final readonly class JavaScriptCode implements \Stringable
{
    public function __construct(
        private string $code,
    ) {}

    public function __toString(): string
    {
        return $this->code;
    }
}
