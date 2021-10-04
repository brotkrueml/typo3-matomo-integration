<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Code;

use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Entity\DataLayerVariable;
use Brotkrueml\MatomoIntegration\Event\AddToDataLayerEvent;
use Brotkrueml\MatomoIntegration\JavaScript\JavaScriptObjectPairCollector;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;

/**
 * @internal
 */
class TagManagerCodeBuilder
{
    private Configuration $configuration;
    private EventDispatcher $eventDispatcher;
    /**
     * @var JavaScriptCode[]
     */
    private array $codeParts = [];
    /**
     * @var DataLayerVariable[]
     */
    private array $dataLayerVariables = [];

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setConfiguration(Configuration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getCode(): string
    {
        $this->initialiseMtmVariable();
        $this->considerDebugMode();
        $this->addStartTimeDataLayerVariable();
        $this->addStartEventDataLayerVariable();
        $this->dispatchAddDataLayerVariable();
        $this->pushDataLayerVariablesToCode();
        $this->addContainerCode();

        return \implode('', $this->codeParts);
    }

    private function initialiseMtmVariable(): void
    {
        $this->codeParts[] = new JavaScriptCode('var _mtm=window._mtm||[];');
    }

    private function considerDebugMode(): void
    {
        if ($this->configuration->tagManagerDebugMode) {
            $this->codeParts[] = new JavaScriptCode('_mtm.push(["enableDebugMode"]);');
        }
    }

    private function addStartTimeDataLayerVariable(): void
    {
        $this->dataLayerVariables[] = new DataLayerVariable('mtm.startTime', new JavaScriptCode('(new Date().getTime())'));
    }

    private function addStartEventDataLayerVariable(): void
    {
        $this->dataLayerVariables[] = new DataLayerVariable('event', 'mtm.Start');
    }

    private function dispatchAddDataLayerVariable(): void
    {
        /** @var AddToDataLayerEvent $event */
        $event = $this->eventDispatcher->dispatch(new AddToDataLayerEvent());
        $this->dataLayerVariables = \array_merge($this->dataLayerVariables, $event->getVariables());
    }

    private function pushDataLayerVariablesToCode(): void
    {
        $collector = new JavaScriptObjectPairCollector();
        foreach ($this->dataLayerVariables as $dataLayer) {
            $collector->addPair($dataLayer->getName(), $dataLayer->getValue());
        }
        $this->codeParts[] = new JavaScriptCode(\sprintf('_mtm.push(%s);', $collector));
    }

    private function addContainerCode(): void
    {
        $this->codeParts[] = new JavaScriptCode(
            'var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];'
            . \sprintf(
                'g.async=true;g.src="%sjs/container_%s.js";s.parentNode.insertBefore(g,s);',
                $this->configuration->url,
                $this->configuration->tagManagerContainerId
            )
        );
    }
}
