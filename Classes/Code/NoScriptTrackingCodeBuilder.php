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
class NoScriptTrackingCodeBuilder
{
    private Configuration $configuration;

    public function setConfiguration(Configuration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getTrackingCode(): string
    {
        /** @noinspection HtmlUnknownTarget */
        return \sprintf(
            '<img src="%smatomo.php?idsite=%d&amp;rec=1" style="border:0;" alt="">',
            $this->configuration->url,
            $this->configuration->siteId
        );
    }
}
