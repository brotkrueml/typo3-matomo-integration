<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Event;

final class BeforeTrackPageViewEvent
{
    private array $code = [];

    public function addCode(string $code): void
    {
        $this->code[] = $code;
    }

    public function getCode(): string
    {
        return \implode('', $this->code);
    }
}
