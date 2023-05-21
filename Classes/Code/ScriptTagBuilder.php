<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Code;

use Brotkrueml\MatomoIntegration\Event\EnrichScriptTagEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Domain\ConsumableString;

/**
 * The tag builder creates the header script tag to include matomo
 *
 * @internal
 */
class ScriptTagBuilder
{
    private ServerRequestInterface $request;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    /**
     * Build the script tag with optional attributes
     */
    public function build(string $code): string
    {
        $attributes = $this->collectAttributes();

        $nonce = $this->request->getAttribute('nonce');
        if ($nonce instanceof ConsumableString) {
            $attributes['nonce'] = $nonce->consume();
        }

        $attributes = array_map(static function (string $name, string $value): string {
            if ($value === '') {
                return $name;
            }
            return $name . '="' . htmlspecialchars($value) . '"';
        }, array_keys($attributes), array_values($attributes));

        $prepend = '';
        if ($attributes !== []) {
            $prepend = ' ';
        }

        return '<script' . $prepend . implode(' ', $attributes) . '>' . $code . '</script>';
    }

    /**
     * Collect all attributes
     *
     * @return array<string,string>
     */
    private function collectAttributes(): array
    {
        $enrichScriptTagEvent = new EnrichScriptTagEvent($this->request);
        /** @var EnrichScriptTagEvent $enrichScriptTagEvent */
        $enrichScriptTagEvent = $this->eventDispatcher->dispatch($enrichScriptTagEvent);

        $attributes = [];

        if ($enrichScriptTagEvent->getId() !== '') {
            $attributes['id'] = $enrichScriptTagEvent->getId();
        }
        if ($enrichScriptTagEvent->getType() !== '') {
            $attributes['type'] = $enrichScriptTagEvent->getType();
        }

        foreach ($enrichScriptTagEvent->getDataAttributes() as $name => $value) {
            $attributes['data-' . $name] = $value;
        }

        return $attributes;
    }
}
