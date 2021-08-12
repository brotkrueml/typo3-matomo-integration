<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Event;

use Brotkrueml\MatomoIntegration\Entity\CustomDimension;

final class EnrichTrackPageViewEvent
{
    private string $pageTitle = '';
    /** @var CustomDimension[] */
    private array $customDimensions = [];

    public function setPageTitle(string $pageTitle): void
    {
        $this->pageTitle = $pageTitle;
    }

    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    public function addCustomDimension(CustomDimension $customDimension): void
    {
        $this->customDimensions[] = $customDimension;
    }

    public function getCustomDimensions(): array
    {
        return $this->customDimensions;
    }
}
