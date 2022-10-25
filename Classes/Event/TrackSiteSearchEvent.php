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
    private ServerRequestInterface $request;
    private string $keyword = '';
    /**
     * @var string|false
     */
    private $category = false;
    /**
     * @var int|false
     */
    private $searchCount = false;
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
     * @return string|false
     * @internal
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string|false $category
     */
    public function setCategory($category): void
    {
        $this->category = $category;
    }

    /**
     * @return int|false
     * @internal
     */
    public function getSearchCount()
    {
        return $this->searchCount;
    }

    /**
     * @param int|false $searchCount
     */
    public function setSearchCount($searchCount): void
    {
        $this->searchCount = $searchCount;
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
