<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Hooks;

use PHPUnit\Runner\BeforeFirstTestHook;

final class Typo3ConstantsHook implements BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        // Needed for PageRenderer mock
        \defined('LF') ?: \define('LF', \chr(10));
    }
}
