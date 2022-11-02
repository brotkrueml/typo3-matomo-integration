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
use Brotkrueml\MatomoIntegration\Entity\DataLayerVariable;
use Psr\Http\Message\ServerRequestInterface;

final class AddToDataLayerEvent
{
    /**
     * @var DataLayerVariable[]
     */
    private array $variables = [];
    private ServerRequestInterface $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @param string|int|float|JavaScriptCode $value
     */
    public function addVariable(string $name, $value): void
    {
        $this->variables[] = new DataLayerVariable($name, $value);
    }

    /**
     * @return DataLayerVariable[]
     * @internal
     */
    public function getVariables(): array
    {
        return $this->variables;
    }
}
