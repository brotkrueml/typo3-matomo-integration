<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Hooks\PageRenderer;

use Brotkrueml\MatomoIntegration\Adapter\ApplicationType;
use Brotkrueml\MatomoIntegration\Hooks\PageRenderer\TrackingCodeInjector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\Entity\Site;

final class TrackingCodeInjectorTest extends TestCase
{
    /** @var Stub|ApplicationType */
    private $applicationTypeStub;

    /** @var Stub|Site */
    private $siteStub;

    /** @var Stub|ServerRequestInterface */
    private $requestStub;

    /** @var MockObject|PageRenderer */
    private $pageRendererMock;

    protected function setUp(): void
    {
        $this->applicationTypeStub = $this->createStub(ApplicationType::class);
        $this->applicationTypeStub
            ->method('isBackend')
            ->willReturn(false);

        $this->siteStub = $this->createStub(Site::class);

        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->requestStub
            ->method('getAttribute')
            ->with('site', [])
            ->willReturn($this->siteStub);

        $this->pageRendererMock = $this->createMock(PageRenderer::class);
    }

    /**
     * @test
     */
    public function executeDoesNothingWhenInBackend(): void
    {
        $applicationTypeStub = $this->createStub(ApplicationType::class);
        $applicationTypeStub
            ->method('isBackend')
            ->willReturn(true);

        $this->pageRendererMock
            ->expects(self::never())
            ->method('addHeaderData');
        $this->pageRendererMock
            ->expects(self::never())
            ->method('addFooterData');

        $subject = new TrackingCodeInjector($applicationTypeStub, $this->requestStub);
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    /**
     * @test
     */
    public function executeDoesNothingWhenConfigurationHasNoValidUrl(): void
    {
        $this->pageRendererMock
            ->expects(self::never())
            ->method('addHeaderData');
        $this->pageRendererMock
            ->expects(self::never())
            ->method('addFooterData');

        $configuration = [
            'matomoIntegrationUrl' => 'some invalid url',
            'matomoIntegrationSiteId' => 42,
        ];

        $this->siteStub
            ->method('getConfiguration')
            ->willReturn($configuration);

        $subject = new TrackingCodeInjector($this->applicationTypeStub, $this->requestStub);
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    /**
     * @test
     */
    public function executeDoesNothingWhenConfigurationHasNoValidSiteId(): void
    {
        $this->pageRendererMock
            ->expects(self::never())
            ->method('addHeaderData');
        $this->pageRendererMock
            ->expects(self::never())
            ->method('addFooterData');

        $configuration = [
            'matomoIntegrationUrl' => 'https://example.org/',
            'matomoIntegrationSiteId' => 0,
        ];

        $this->siteStub
            ->method('getConfiguration')
            ->willReturn($configuration);

        $subject = new TrackingCodeInjector($this->applicationTypeStub, $this->requestStub);
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    /**
     * @test
     */
    public function executeAddsJavaScriptTrackingCodeToHeaderDataCorrectly(): void
    {
        $this->pageRendererMock
            ->expects(self::once())
            ->method('addHeaderData')
            ->with(self::callback(function ($subject) {
                return \str_starts_with($subject, '<script>var _paq=window._paq||[];')
                    && \str_ends_with($subject, '</script>');
            }));
        $this->pageRendererMock
            ->expects(self::never())
            ->method('addFooterData');

        $configuration = [
            'matomoIntegrationUrl' => 'https://example.org/',
            'matomoIntegrationSiteId' => 42,
        ];

        $this->siteStub
            ->method('getConfiguration')
            ->willReturn($configuration);

        $subject = new TrackingCodeInjector($this->applicationTypeStub, $this->requestStub);
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    /**
     * @test
     */
    public function executeAddsNoScriptTrackingCodeToFooterDataCorrectly(): void
    {
        $this->pageRendererMock
            ->expects(self::once())
            ->method('addHeaderData');
        $this->pageRendererMock
            ->expects(self::once())
            ->method('addFooterData')
            ->with(self::callback(function ($subject) {
                /** @noinspection RequiredAttributes,HtmlRequiredAltAttribute */
                return \str_starts_with($subject, '<noscript><img src')
                    && \str_ends_with($subject, '</noscript>');
            }));

        $configuration = [
            'matomoIntegrationUrl' => 'https://example.org/',
            'matomoIntegrationSiteId' => 42,
            'matomoIntegrationNoScript' => true,
        ];

        $this->siteStub
            ->method('getConfiguration')
            ->willReturn($configuration);

        $subject = new TrackingCodeInjector($this->applicationTypeStub, $this->requestStub);
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }
}
