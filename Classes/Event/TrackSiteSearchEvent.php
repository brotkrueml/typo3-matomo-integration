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

final class TrackSiteSearchEvent
{
    private string $keyword = '';
    private string|false $category = false;
    private int|false $searchCount = false;
    /**
     * @var list<CustomDimension>
     */
    private array $customDimensions = [];

    public function __construct(
        private readonly ServerRequestInterface $request,
    ) {}

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @internal
     */
    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }

    /**
     * @internal
     */
    public function getCategory(): string|false
    {
        return $this->category;
    }

    public function setCategory(string|false $category): void
    {
        $this->category = $category;
    }

    /**
     * @internal
     */
    public function getSearchCount(): int|false
    {
        return $this->searchCount;
    }

    public function setSearchCount(int|false $searchCount): void
    {
        $this->searchCount = $searchCount;
    }

    public function addCustomDimension(int $id, string $value): void
    {
        $this->customDimensions[] = new CustomDimension($id, $value);
    }

    /**
     * @return list<CustomDimension>
     * @internal
     */
    public function getCustomDimensions(): array
    {
        return $this->customDimensions;
    }
}
