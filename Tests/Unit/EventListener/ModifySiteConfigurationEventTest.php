<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\EventListener;

use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Event\ModifySiteConfigurationEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class ModifySiteConfigurationEventTest extends TestCase
{
    private ServerRequestInterface&Stub $requestDummy;
    private ModifySiteConfigurationEvent $subject;

    protected function setUp(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://example.org/',
            'matomoIntegrationSiteId' => 1,
            'matomoIntegrationTagManagerContainerIds' => 'someId',
        ]);

        $this->requestDummy = $this->createStub(ServerRequestInterface::class);

        $this->subject = new ModifySiteConfigurationEvent(
            $this->requestDummy,
            $configuration,
            'some_site',
        );
    }

    #[Test]
    public function getRequest(): void
    {
        $actual = $this->subject->getRequest();

        self::assertSame($actual, $this->requestDummy);
    }

    #[Test]
    public function getSiteIdentifier(): void
    {
        $actual = $this->subject->getSiteIdentifier();

        self::assertSame('some_site', $actual);
    }

    #[Test]
    public function getUrl(): void
    {
        $actual = $this->subject->getUrl();

        self::assertSame('https://example.org/', $actual);
    }

    #[Test]
    public function setUrl(): void
    {
        $this->subject->setUrl('https://example.com/');

        $actual = $this->subject->getUrl();

        self::assertSame('https://example.com/', $actual);
    }

    #[Test]
    public function getSiteId(): void
    {
        $actual = $this->subject->getSiteId();

        self::assertSame(1, $actual);
    }

    #[Test]
    public function setSiteId(): void
    {
        $this->subject->setSiteId(42);

        $actual = $this->subject->getSiteId();

        self::assertSame(42, $actual);
    }

    #[Test]
    public function getTagManagerContainerIds(): void
    {
        $actual = $this->subject->getTagManagerContainerIds();

        self::assertSame(['someId'], $actual);
    }

    #[Test]
    public function setTagManagerContainerIds(): void
    {
        $this->subject->setTagManagerContainerIds(['anotherId']);

        $actual = $this->subject->getTagManagerContainerIds();

        self::assertSame(['anotherId'], $actual);
    }

    #[Test]
    public function getConfiguration(): void
    {
        $actual = $this->subject->getConfiguration();

        self::assertSame('https://example.org/', $actual->url);
        self::assertSame(1, $actual->siteId);
        self::assertSame(['someId'], $actual->tagManagerContainerIds);
    }
}
