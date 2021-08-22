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

/**
 * @internal
 */
class TagManagerCodeBuilder
{
    private Configuration $configuration;
    /** @var list<JavaScriptCode|MatomoMethodCall> */
    private array $codeParts = [];

    public function setConfiguration(Configuration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getCode(): string
    {
        $this->initialise();
        $this->addStartEvent();
        $this->addContainerCode();

        return \implode('', $this->codeParts);
    }

    private function initialise(): void
    {
        $this->codeParts[] = new JavaScriptCode('var _mtm=window._mtm||[];');
    }

    private function addStartEvent(): void
    {
        $this->codeParts[] = new JavaScriptCode('_mtm.push({"mtm.startTime":(new Date().getTime()),"event":"mtm.Start"});');
    }

    private function addContainerCode(): void
    {
        $this->codeParts[] = new JavaScriptCode(
            'var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];'
            . \sprintf(
                'g.async=true;g.src="%sjs/container_%s.js";s.parentNode.insertBefore(g,s);',
                \rtrim($this->configuration->url, '/') . '/',
                $this->configuration->tagManagerContainerId
            )
        );
    }
}
