<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Event;

use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Normalisation\UrlNormaliser;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ModifySiteConfigurationEvent
{
    public function __construct(
        private ServerRequestInterface $request,
        private Configuration $configuration,
        private string $siteIdentifier,
    ) {}

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getSiteIdentifier(): string
    {
        return $this->siteIdentifier;
    }

    public function getUrl(): string
    {
        return $this->configuration->url;
    }

    public function setUrl(string $url): void
    {
        $this->configuration->url = UrlNormaliser::normalise($url);
    }

    public function getSiteId(): int
    {
        return $this->configuration->siteId;
    }

    public function setSiteId(int $siteId): void
    {
        $this->configuration->siteId = $siteId;
    }

    /**
     * @return list<string>
     */
    public function getTagManagerContainerIds(): array
    {
        return $this->configuration->tagManagerContainerIds;
    }

    /**
     * @param list<string> $containerIds
     */
    public function setTagManagerContainerIds(array $containerIds): void
    {
        $this->configuration->tagManagerContainerIds = $containerIds;
    }

    /**
     * @internal Only to be used in this extension, no public API!
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }
}
