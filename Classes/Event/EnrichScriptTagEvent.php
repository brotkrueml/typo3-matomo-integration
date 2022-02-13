<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Event;

use Brotkrueml\MatomoIntegration\Exceptions\InvalidDataAttributeName;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This event allows the developer to enrich the build script tag with additional attributes.
 */
final class EnrichScriptTagEvent
{
    /**
     * The server request in case the developer requires additional information.
     */
    private ServerRequestInterface $request;

    /**
     * The id of the element
     */
    private string $id = '';

    /**
     * The type that should be set for the script tag.
     * e.g. text/plain to not execute script on page load, but a consent manager should handle this.
     */
    private string $type = '';

    /**
     * Additional data attributes.
     * @var array <string, string>
     */
    private array $dataAttributes = [];

    public function __construct(ServerRequestInterface $serverRequest)
    {
        $this->request = $serverRequest;
    }

    /**
     * The server request for more context
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Set the id of script tag.
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Returns the id of the script tag element
     *
     * @internal
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @internal
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * Returns the data attribute array
     *
     * @return array<string, string>
     * @internal
     */
    public function getDataAttributes(): array
    {
        return $this->dataAttributes;
    }

    /**
     * Add a data attribute to the script tag.
     * The prefix `data-` should be not used as part of the attribute name.
     *
     * @throws InvalidDataAttributeName
     */
    public function addDataAttribute(string $name, string $value = ''): void
    {
        if (str_starts_with(strtolower($name), 'data-')) {
            throw new InvalidDataAttributeName('Name should not starts with data-', 1644869412);
        }
        $pattern = '\s"\'>=' . preg_quote('\\', '/');

        if (preg_match('/[' . $pattern . ']/', $name)) {
            throw new InvalidDataAttributeName(
                'Name should not contains a whitespace, quotes, backslashes, equal sign and closed pointed bracket',
                1644869542
            );
        }

        $this->dataAttributes[$name] = $value;
    }
}
