<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Middleware;

use Brotkrueml\MatomoIntegration\Code\JavaScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Code\NoScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Code\TagManagerCodeBuilder;
use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
final class TrackingCodeInjection implements MiddlewareInterface
{
    private JavaScriptTrackingCodeBuilder $javaScriptTrackingCodeBuilder;
    private NoScriptTrackingCodeBuilder $noScriptTrackingCodeBuilder;
    private TagManagerCodeBuilder $tagManagerCodeBuilder;

    public function __construct(
        JavaScriptTrackingCodeBuilder $javaScriptTrackingCodeBuilder,
        NoScriptTrackingCodeBuilder $noScriptTrackingCodeBuilder,
        TagManagerCodeBuilder $tagManagerCodeBuilder
    ) {
        $this->javaScriptTrackingCodeBuilder = $javaScriptTrackingCodeBuilder;
        $this->noScriptTrackingCodeBuilder = $noScriptTrackingCodeBuilder;
        $this->tagManagerCodeBuilder = $tagManagerCodeBuilder;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $contentType = $response->getHeader('Content-Type')[0] ?? '';
        if (!\str_starts_with($contentType, 'text/html')) {
            return $response;
        }

        /** @var Site $site */
        $site = $request->getAttribute('site');
        $configuration = Configuration::createFromSiteConfiguration($site->getConfiguration());

        if (!$this->hasValidConfiguration($configuration)) {
            return $response;
        }

        $scriptCode = $this->javaScriptTrackingCodeBuilder->setConfiguration($configuration)->getTrackingCode();
        if ($configuration->tagManagerContainerId !== '') {
            $scriptCode .= $this->tagManagerCodeBuilder->setConfiguration($configuration)->getCode();
        }

        $body = $response->getBody();
        $body->rewind();
        $contents = \str_replace(
            '<title>',
            \sprintf("<script>%s</script>\n", $scriptCode) . '<title>',
            $body->getContents()
        );

        if ($configuration->noScript) {
            $noScriptCode = $this->noScriptTrackingCodeBuilder->setConfiguration($configuration)->getTrackingCode();
            $contents = \str_replace(
                '</body>',
                \sprintf("<noscript>%s</noscript>\n", $noScriptCode) . '</body>',
                $contents
            );
        }

        $body->rewind();
        $body->write($contents);

        return $response;
    }

    private function hasValidConfiguration(Configuration $configuration): bool
    {
        if (!\filter_var($configuration->url, \FILTER_VALIDATE_URL)) {
            return false;
        }

        return $configuration->siteId > 0;
    }
}
