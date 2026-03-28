<?php

declare(strict_types=1);

namespace YourVendor\YourExtension\EventListener;

use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

#[AsEventListener(
    identifier: 'your-ext/remove-token-from-url-for-matomo-tracking',
)]
final readonly class RemoveTokenFromUrlForMatomoTracking
{
    public function __invoke(BeforeTrackPageViewEvent $event)
    {
        $request = $event->getRequest();
        if (! isset($request->getQueryParams()['tx_myext']['token'])) {
            return;
        }

        $uri = $request->getUri();
        $pathParts = \explode('/', $uri->getPath());
        // The path ends with a slash, which we want to preserve, so we
        // need to remove the second last part (which is the token)
        unset($pathParts[\count($pathParts) - 2]);
        $tokenRemovedUri = $uri->withPath(\implode('/', $pathParts));

        $event->addMatomoMethodCall('setCustomUrl', (string) $tokenRemovedUri);
    }
}
