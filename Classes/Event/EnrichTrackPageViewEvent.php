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
use Psr\Http\Message\ServerRequestInterface;

final class EnrichTrackPageViewEvent
{
    private ServerRequestInterface $request;
    private string $pageTitle = '';
    /**
     * @var CustomDimension[]
     */
    private array $customDimensions = [];

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function setPageTitle(string $pageTitle): void
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * @internal
     */
    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    public function addCustomDimension(int $id, string $value): void
    {
        $this->customDimensions[] = new CustomDimension($id, $value);
    }

    /**
     * @return CustomDimension[]
     * @internal
     */
    public function getCustomDimensions(): array
    {
        return $this->customDimensions;
    }
}
