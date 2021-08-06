<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Domain\TrackingCode;

use Brotkrueml\MatomoIntegration\Domain\Dto\Configuration;

final class NoScriptTrackingCodeBuilder
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getTrackingCode(): string
    {
        /** @noinspection HtmlUnknownTarget */
        return \sprintf(
            '<img src="%s/matomo.php?idsite=%d&amp;rec=1" style="border:0;" alt="">',
            \rtrim($this->configuration->url, '/'),
            $this->configuration->siteId
        );
    }
}
