<?php

declare(strict_types=1);

namespace YourVender\YourExtension\Matomo;

use Brotkrueml\MatomoIntegration\Code\JavaScriptCode;
use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;

final class SetDocumentTitleExample
{
    public function __invoke(BeforeTrackPageViewEvent $event): void
    {
        // Set the document title
        $event->addMatomoMethodCall('setDocumentTitle', 'Some Document Title');

        // OR:
        // Add some JavaScript code
        $event->addJavaScriptCode('function getDocumentTitle { return "Some Document Title"; }');
        // Set the document title
        $event->addMatomoMethodCall('setDocumentTitle', new JavaScriptCode('getDocumentTitle()'));
    }
}
