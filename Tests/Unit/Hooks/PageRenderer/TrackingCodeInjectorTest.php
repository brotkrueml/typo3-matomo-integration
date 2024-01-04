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
use Brotkrueml\MatomoIntegration\Hooks\PageRenderer\TrackingCodeInjector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\Entity\Site;

final class TrackingCodeInjectorTest extends TestCase
{
    /**
     * @var Stub&Site
     */
    private Stub $siteStub;

    /**
     * @var Stub&ServerRequestInterface
     */
    private Stub $requestStub;

    /**
     * @var Stub&JavaScriptTrackingCodeBuilder
     */
    private Stub $javaScriptTrackingCodeBuilderStub;

    /**
     * @var Stub&NoScriptTrackingCodeBuilder
     */
    private Stub $noScriptTrackingCodeBuilderStub;

    /**
     * @var Stub&TagManagerCodeBuilder
     */
    private Stub $tagManagerCodeBuilderStub;

    /**
     * @var MockObject&PageRenderer
     */
    private MockObject $pageRendererMock;

    /**
     * @var Stub&ScriptTagBuilder
     */
    private Stub $scriptTagBuilderStub;

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

        $eventDispatcher = new class() implements EventDispatcherInterface {
            public function dispatch(object $event, string $eventName = null): object
            {
                return $event;
            }
        };

        $this->scriptTagBuilder = new ScriptTagBuilder($eventDispatcher);

        $GLOBALS['TYPO3_REQUEST'] = $this->requestStub;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TYPO3_REQUEST']);
    }

    /**
     * @test
     */
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
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    /**
     * @test
     */
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
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    /**
     * @test
     */
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
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    /**
     * @test
     */
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
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    /**
     * @test
     */
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
            'matomoIntegrationTagManagerContainerId' => 'someId',
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
        );
        $params = [];
        $subject->execute($params, $this->pageRendererMock);
    }

    /**
     * @test
     */
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
