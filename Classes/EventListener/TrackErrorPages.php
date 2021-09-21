<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\EventListener;

use Brotkrueml\MatomoIntegration\Code\JavaScriptCode;
use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @internal
 */
final class TrackErrorPages
{
    private ServerRequestInterface $request;

    /**
     * Arguments for testing purposes!
     */
    public function __construct(ServerRequestInterface $request = null)
    {
        $this->request = $request ?? $GLOBALS['TYPO3_REQUEST'];
    }

    public function __invoke(BeforeTrackPageViewEvent $event): void
    {
        if (!$event->getConfiguration()->trackErrorPages) {
            return;
        }

        $errorHandlers = $this->request->getAttribute('site')->getConfiguration()['errorHandling'] ?? [];
        if ($errorHandlers === []) {
            return;
        }

        $pageId = $this->request->getAttribute('routing')->getPageId();
        $errorHandlerForPage = \array_filter(
            $errorHandlers,
            static fn (array $handler): bool => $handler['errorHandler'] === 'Page' && $handler['errorContentSource'] === 't3://page?uid=' . $pageId
        );
        if ($errorHandlerForPage === []) {
            return;
        }

        $event->addMatomoMethodCall(
            'setDocumentTitle',
            new JavaScriptCode(
                \sprintf(
                    '"%d/URL = "+encodeURIComponent(document.location.pathname+document.location.search)+"/From = "+encodeURIComponent(document.referrer)',
                    $errorHandlerForPage[0]['errorCode']
                )
            )
        );
    }
}
