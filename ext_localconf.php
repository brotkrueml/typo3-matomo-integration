<?php

declare(strict_types=1);

use Brotkrueml\MatomoIntegration\Hooks\PageRenderer\TrackingCodeInjector;

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-postTransform'][] =
    TrackingCodeInjector::class . '->execute';
