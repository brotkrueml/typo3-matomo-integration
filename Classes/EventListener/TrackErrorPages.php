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
use Brotkrueml\MatomoIntegration\Extension;
use TYPO3\CMS\Core\Attribute\AsEventListener;

/**
 * @internal
 */
#[AsEventListener(
    identifier: 'matomo-integration/track-error-pages',
)]
final readonly class TrackErrorPages
{
    public function __invoke(BeforeTrackPageViewEvent $event): void
    {
        if (! $event->getConfiguration()->trackErrorPages) {
            return;
        }

        $errorHandlers = $event->getRequest()->getAttribute('site')->getConfiguration()['errorHandling'] ?? [];
        if ($errorHandlers === []) {
            return;
        }

        $pageId = $event->getRequest()->getAttribute('routing')->getPageId();
        $errorHandlersForPage = \array_values(\array_filter(
            $errorHandlers,
            static fn(array $handler): bool => $handler['errorHandler'] === 'Page' && $handler['errorContentSource'] === 't3://page?uid=' . $pageId,
        ));
        if ($errorHandlersForPage === []) {
            return;
        }

        $template = $event->getConfiguration()->errorPagesTemplate ?: Extension::DEFAULT_TEMPLATE_ERROR_PAGES;
        $templateVariables = [
            '{statusCode}' => $errorHandlersForPage[0]['errorCode'],
            '{path}' => '"+encodeURIComponent(document.location.pathname+document.location.search)+"',
            '{referrer}' => '"+encodeURIComponent(document.referrer)+"',
        ];
        $sanitisedDocumentTitle = \str_replace(
            \array_keys($templateVariables),
            \array_values($templateVariables),
            \addcslashes($template, '"'),
        );

        $event->addMatomoMethodCall('setDocumentTitle', new JavaScriptCode('"' . $sanitisedDocumentTitle . '"'));
    }
}
