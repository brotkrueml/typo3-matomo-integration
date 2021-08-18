<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Code;

use Brotkrueml\MatomoIntegration\Code\JavaScriptCode;
use PHPUnit\Framework\TestCase;

final class JavaScriptCodeTest extends TestCase
{
    /**
     * @test
     */
    public function toStringReturnsCodeCorrectly(): void
    {
        $subject = new JavaScriptCode('/* some code */');

        self::assertSame('/* some code */', (string)$subject);
    }
}
