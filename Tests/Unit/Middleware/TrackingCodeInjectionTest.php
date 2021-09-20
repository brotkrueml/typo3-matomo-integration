<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Middleware;

use Brotkrueml\MatomoIntegration\Code\JavaScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Code\NoScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Code\TagManagerCodeBuilder;
use Brotkrueml\MatomoIntegration\Middleware\TrackingCodeInjection;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Site\Entity\Site;

final class TrackingCodeInjectionTest extends TestCase
{
    private const ORIGINAL_RESPONSE_CONTENTS = '<html lang="en"><head><title>Some title</title></head><body>Some body</body></html>';

    /** @var Stub|Site */
    private $siteStub;

    /** @var Stub|ServerRequestInterface */
    private $requestStub;

    /** @var Stub|RequestHandlerInterface */
    private $handlerStub;

    private Response $response;

    /** @var Stub|JavaScriptTrackingCodeBuilder */
    private $javaScriptTrackingCodeBuilderStub;

    /** @var Stub|NoScriptTrackingCodeBuilder */
    private $noScriptTrackingCodeBuilderStub;

    /** @var Stub|TagManagerCodeBuilder */
    private $tagManagerCodeBuilderStub;

    protected function setUp(): void
    {
        $this->siteStub = $this->createStub(Site::class);

        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->requestStub
            ->method('getAttribute')
            ->with('site')
            ->willReturn($this->siteStub);

        $this->response = new Response();
        $this->response = $this->response->withHeader('Content-Type', 'text/html; charset=utf-8');
        $this->response->getBody()->write(self::ORIGINAL_RESPONSE_CONTENTS);

        $this->handlerStub = $this->createStub(RequestHandlerInterface::class);
        $this->handlerStub
            ->method('handle')
            ->with($this->requestStub)
            ->willReturn($this->response);

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
    }

    /**
     * @test
     */
    public function processReturnsOriginalResponseContentsWhenContentTypeIsNotTextHtml(): void
    {
        $this->response = $this->response->withHeader('Content-Type', 'application/json');

        $subject = new TrackingCodeInjection(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub
        );

        $actual = $subject->process($this->requestStub, $this->handlerStub);
        $body = $actual->getBody();
        $body->rewind();

        self::assertSame(self::ORIGINAL_RESPONSE_CONTENTS, $body->getContents());
    }

    /**
     * @test
     */
    public function processReturnsOriginalResponseContentsWhenConfigurationHasNoValidUrl(): void
    {
        $configuration = [
            'matomoIntegrationUrl' => 'some invalid url',
            'matomoIntegrationSiteId' => 42,
        ];

        $this->siteStub
            ->method('getConfiguration')
            ->willReturn($configuration);

        $subject = new TrackingCodeInjection(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub
        );

        $actual = $subject->process($this->requestStub, $this->handlerStub);
        $body = $actual->getBody();
        $body->rewind();

        self::assertSame(self::ORIGINAL_RESPONSE_CONTENTS, $body->getContents());
    }

    /**
     * @test
     */
    public function processReturnsOriginalResponseContentsWhenConfigurationHasNoValidSiteId(): void
    {
        $configuration = [
            'matomoIntegrationUrl' => 'https://example.org/',
            'matomoIntegrationSiteId' => 0,
        ];

        $this->siteStub
            ->method('getConfiguration')
            ->willReturn($configuration);

        $subject = new TrackingCodeInjection(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub
        );

        $actual = $subject->process($this->requestStub, $this->handlerStub);
        $body = $actual->getBody();
        $body->rewind();

        self::assertSame(self::ORIGINAL_RESPONSE_CONTENTS, $body->getContents());
    }

    /**
     * @test
     */
    public function processInjectsJavaScriptTrackingCodeWithoutTagManagerIntoHeadCorrectly(): void
    {
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

        $subject = new TrackingCodeInjection(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub
        );

        $actual = $subject->process($this->requestStub, $this->handlerStub);
        $body = $actual->getBody();
        $body->rewind();

        self::assertSame(
            '<html lang="en"><head><script>/* some tracking code */</script>
<title>Some title</title></head><body>Some body</body></html>',
            $body->getContents()
        );
    }

    /**
     * @test
     */
    public function processIbjectsJavaScriptTrackingCodeWithTagManagerIntoHeadCorrectly(): void
    {
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

        $subject = new TrackingCodeInjection(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub
        );

        $actual = $subject->process($this->requestStub, $this->handlerStub);
        $body = $actual->getBody();
        $body->rewind();

        self::assertSame(
            '<html lang="en"><head><script>/* some tracking code *//* some tag manager code */</script>
<title>Some title</title></head><body>Some body</body></html>',
            $body->getContents()
        );
    }

    /**
     * @test
     */
    public function processInjectsNoScriptTrackingCodeIntoBodyCorrectly(): void
    {
        $configuration = [
            'matomoIntegrationUrl' => 'https://example.org/',
            'matomoIntegrationSiteId' => 42,
            'matomoIntegrationNoScript' => true,
        ];

        $this->siteStub
            ->method('getConfiguration')
            ->willReturn($configuration);

        $this->javaScriptTrackingCodeBuilderStub
            ->method('getTrackingCode')
            ->willReturn('/* some tracking code */');

        $this->noScriptTrackingCodeBuilderStub
            ->method('getTrackingCode')
            ->willReturn('<!-- some tracking code -->');

        $subject = new TrackingCodeInjection(
            $this->javaScriptTrackingCodeBuilderStub,
            $this->noScriptTrackingCodeBuilderStub,
            $this->tagManagerCodeBuilderStub
        );

        $actual = $subject->process($this->requestStub, $this->handlerStub);
        $body = $actual->getBody();
        $body->rewind();

        self::assertSame(
            '<html lang="en"><head><script>/* some tracking code */</script>
<title>Some title</title></head><body>Some body<noscript><!-- some tracking code --></noscript>
</body></html>',
            $body->getContents()
        );
    }
}
