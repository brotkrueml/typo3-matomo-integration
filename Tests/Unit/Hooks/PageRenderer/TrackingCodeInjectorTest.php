<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Hooks\PageRenderer;

use Brotkrueml\MatomoIntegration\Code\JavaScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Code\NoScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Code\ScriptTagBuilder;
use Brotkrueml\MatomoIntegration\Code\TagManagerCodeBuilder;
use Brotkrueml\MatomoIntegration\Event\ModifySiteConfigurationEvent;
use Brotkrueml\MatomoIntegration\Hooks\PageRenderer\TrackingCodeInjector;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\EventDispatcher\NoopEventDispatcher;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\Entity\Site;

final class TrackingCodeInjectorTest extends TestCase
{
    private Stub&Site $siteStub;
    private Stub&ServerRequestInterface $requestStub;
    private Stub&JavaScriptTrackingCodeBuilder $javaScriptTrackingCodeBuilderStub;
    private Stub&NoScriptTrackingCodeBuilder $noScriptTrackingCodeBuilderStub;
    private Stub&TagManagerCodeBuilder $tagManagerCodeBuilderStub;
    private MockObject&PageRenderer $pageRendererMock;
    private Stub&ScriptTagBuilder $scriptTagBuilderStub;
    private ScriptTagBuilder $scriptTagBuilder;

    protected function setUp(): void
    {
        $this->siteStub = $this->createStub(Site::class);

        $this->requestStub = $this->createStub(ServerRequestInterface::class);

        $this->javaScriptTrackingCodeBuilderStub = $this->createStub(JavaScriptTrackingCodeBuilder::class);
        $this->javaScriptTrackingCodeBuilderStub
            ->method('setConfiguration')
            ->willReturn($this->javaScriptTrackingCodeBuilderStub);

        $this->noScriptTrackingCodeBuilderStub = $this->createStub(NoScriptTrackingCodeBuilder::class);
        $this->noScriptTrackingCodeBuilderStub
            ->method('setConfiguration')
            ->willReturn($this->noScriptTrackingCodeBuilderStub);

        $this->tagManagerCodeBuilderStub = $this->createStub(TagManagerCodeBuilder::class);
        $this->tagManagerCodeBuilderStub
            ->method('setConfiguration')
            ->willReturn($this->tagManagerCodeBuilderStub);

        $this->pageRendererMock = $this->createMock(PageRenderer::class);

        $this->scriptTagBuilderStub = $this->createStub(ScriptTagBuilder::class);
        $this->scriptTagBuilderStub
            ->method('build')
            ->willReturn('<script></script>');

        $this->scriptTagBuilder = new ScriptTagBuilder(new NoopEventDispatcher());

        $GLOBALS['TYPO3_REQUEST'] = $this->requestStub;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
    }

    #[Test]
    public function executeDoesNothingWhenInBackend(): void
    {
        $this->requestStub
            ->method('getAttribute')
            ->with('applicationType')
            ->willReturn(SystemEnvironmentBuilder::REQUESTTYPE_BE);

        $this->pageRendererMock
            ->expects(self::never())
            ->method('addHeaderData');
        $this->pageRendererMock
            ->expects(self::never())
            ->method('addFooterData');

        $subject = new TrackingCodeInjector(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub,
            $this->scriptTagBuilderStub,
            new NoopEventDispatcher(),
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    #[Test]
    public function executeDoesNothingWhenConfigurationHasNoValidUrl(): void
    {
        $this->configureDefaultRequestStubForFrontend();

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

        $subject = new TrackingCodeInjector(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub,
            $this->scriptTagBuilderStub,
            new NoopEventDispatcher(),
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    #[Test]
    public function executeDoesNothingWhenConfigurationHasNoValidSiteId(): void
    {
        $this->configureDefaultRequestStubForFrontend();

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

        $subject = new TrackingCodeInjector(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub,
            $this->scriptTagBuilderStub,
            new NoopEventDispatcher(),
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    public function executeAcceptsUrlWithRelativeProtocol(): void
    {
        $this->configureDefaultRequestStubForFrontend();

        $this->pageRendererMock
            ->expects(self::once())
            ->method('addHeaderData')
            ->with('<script>/* some tracking code */</script>');
        $this->pageRendererMock
            ->expects(self::never())
            ->method('addFooterData');

        $configuration = [
            'matomoIntegrationUrl' => '//example.org/',
            'matomoIntegrationSiteId' => 42,
        ];

        $this->siteStub
            ->method('getConfiguration')
            ->willReturn($configuration);

        $this->javaScriptTrackingCodeBuilderStub
            ->method('getTrackingCode')
            ->willReturn('/* some tracking code */');

        $subject = new TrackingCodeInjector(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub,
            $this->scriptTagBuilder,
            new NoopEventDispatcher(),
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    #[Test]
    public function executeAddsJavaScriptTrackingCodeWithoutTagManagerToHeaderDataCorrectly(): void
    {
        $this->configureDefaultRequestStubForFrontend();

        $this->pageRendererMock
            ->expects(self::once())
            ->method('addHeaderData')
            ->with('<script>/* some tracking code */</script>');
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

        $this->javaScriptTrackingCodeBuilderStub
            ->method('getTrackingCode')
            ->willReturn('/* some tracking code */');

        $subject = new TrackingCodeInjector(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub,
            $this->scriptTagBuilder,
            new NoopEventDispatcher(),
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    #[Test]
    public function executeAddsJavaScriptTrackingCodeWithTagManagerToHeaderDataCorrectly(): void
    {
        $this->configureDefaultRequestStubForFrontend();

        $this->pageRendererMock
            ->expects(self::once())
            ->method('addHeaderData')
            ->with('<script>/* some tracking code *//* some tag manager code */</script>');
        $this->pageRendererMock
            ->expects(self::never())
            ->method('addFooterData');

        $configuration = [
            'matomoIntegrationUrl' => 'https://example.org/',
            'matomoIntegrationSiteId' => 42,
            'matomoIntegrationTagManagerContainerIds' => 'someId',
        ];

        $this->siteStub
            ->method('getConfiguration')
            ->willReturn($configuration);

        $this->javaScriptTrackingCodeBuilderStub
            ->method('getTrackingCode')
            ->willReturn('/* some tracking code */');

        $this->tagManagerCodeBuilderStub
            ->method('getCode')
            ->willReturn('/* some tag manager code */');

        $subject = new TrackingCodeInjector(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub,
            $this->scriptTagBuilder,
            new NoopEventDispatcher(),
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    #[Test]
    public function executeAddsNoScriptTrackingCodeToFooterDataCorrectly(): void
    {
        $this->configureDefaultRequestStubForFrontend();

        $this->pageRendererMock
            ->expects(self::once())
            ->method('addHeaderData');
        $this->pageRendererMock
            ->expects(self::once())
            ->method('addFooterData')
            ->with('<noscript><!-- some tracking code --></noscript>');

        $configuration = [
            'matomoIntegrationUrl' => 'https://example.org/',
            'matomoIntegrationSiteId' => 42,
            'matomoIntegrationNoScript' => true,
        ];

        $this->siteStub
            ->method('getConfiguration')
            ->willReturn($configuration);

        $this->noScriptTrackingCodeBuilderStub
            ->method('getTrackingCode')
            ->willReturn('<!-- some tracking code -->');

        $subject = new TrackingCodeInjector(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub,
            $this->scriptTagBuilderStub,
            new NoopEventDispatcher(),
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    #[Test]
    public function ensureModifySiteConfigurationEventIsDispatched(): void
    {
        $this->configureDefaultRequestStubForFrontend();

        $this->pageRendererMock
            ->expects(self::once())
            ->method('addHeaderData');
        $this->pageRendererMock
            ->expects(self::once())
            ->method('addFooterData')
            ->with('<noscript><!-- some tracking code --></noscript>');

        $configuration = [
            'matomoIntegrationUrl' => 'https://example.org/',
            'matomoIntegrationSiteId' => 42,
            'matomoIntegrationNoScript' => true,
        ];

        $this->siteStub
            ->method('getConfiguration')
            ->willReturn($configuration);
        $this->siteStub
            ->method('getIdentifier')
            ->willReturn('some_site');

        $this->noScriptTrackingCodeBuilderStub
            ->method('getTrackingCode')
            ->willReturn('<!-- some tracking code -->');

        $eventDispatcherMock = self::createMock(EventDispatcherInterface::class);
        $eventDispatcherMock
            ->expects(self::once())
            ->method('dispatch')
            ->willReturnCallback(static function (object $event): object {
                if (! $event instanceof ModifySiteConfigurationEvent) {
                    self::fail('event is not ModifySiteConfigurationEvent');
                }
                return $event;
            });

        $subject = new TrackingCodeInjector(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub,
            $this->scriptTagBuilderStub,
            $eventDispatcherMock,
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    private function configureDefaultRequestStubForFrontend(): void
    {
        $this->requestStub
            ->method('getAttribute')
            ->willReturnCallback(function (string $attribute): int|Stub|null {
                if ($attribute === 'applicationType') {
                    return SystemEnvironmentBuilder::REQUESTTYPE_FE;
                }
                if ($attribute === 'site') {
                    return $this->siteStub;
                }
                if ($attribute === 'nonce') {
                    return null;
                }
                throw new \InvalidArgumentException('Attribute "' . $attribute . '" not considered in stub callback');
            });
    }
}
