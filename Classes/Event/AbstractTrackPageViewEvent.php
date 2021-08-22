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
    private array $javaScriptCodes = [];
    /** @var MatomoMethodCall[] */
    private array $matomoMethodCalls = [];

    public function addJavaScriptCode(string $code): void
    {
        $this->javaScriptCodes[] = new JavaScriptCode($code);
    }

    /**
     * @param array|bool|int|string|JavaScriptCode ...$parameters
     */
    public function addMatomoMethodCall(string $method, ...$parameters): void
    {
        $this->matomoMethodCalls[] = new MatomoMethodCall($method, ...$parameters);
    }

    /**
     * @return JavaScriptCode[]
     * @internal
     */
    public function getJavaScriptCodes(): array
    {
        return $this->javaScriptCodes;
    }

    /**
     * @return MatomoMethodCall[]
     * @internal
     */
    public function getMatomoMethodCalls(): array
    {
        return $this->matomoMethodCalls;
    }
}
