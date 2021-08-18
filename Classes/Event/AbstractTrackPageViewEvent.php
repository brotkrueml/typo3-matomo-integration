<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Event;

use Brotkrueml\MatomoIntegration\Code\JavaScriptCode;
use Brotkrueml\MatomoIntegration\Code\MatomoMethodCall;

/**
 * @internal
 */
abstract class AbstractTrackPageViewEvent
{
    /** @var JavaScriptCode[] */
    private array $javaScriptCode = [];
    /** @var MatomoMethodCall[] */
    private array $matomoMethodCall = [];

    public function addJavaScriptCode(JavaScriptCode $code): void
    {
        $this->javaScriptCode[] = $code;
    }

    public function addMatomoMethodCall(MatomoMethodCall $call): void
    {
        $this->matomoMethodCall[] = $call;
    }

    public function getCode(): string
    {
        return \implode('', \array_merge($this->javaScriptCode, $this->matomoMethodCall));
    }
}
