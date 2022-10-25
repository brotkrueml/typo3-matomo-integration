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
use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @internal
 */
abstract class AbstractTrackPageViewEvent
{
    private Configuration $configuration;
    private ServerRequestInterface $request;
    /**
     * @var JavaScriptCode[]
     */
    private array $javaScriptCodes = [];
    /**
     * @var MatomoMethodCall[]
     */
    private array $matomoMethodCalls = [];

    public function __construct(Configuration $configuration, ServerRequestInterface $request)
    {
        $this->configuration = $configuration;
        $this->request = $request;
    }

    /**
     * @internal
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

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
