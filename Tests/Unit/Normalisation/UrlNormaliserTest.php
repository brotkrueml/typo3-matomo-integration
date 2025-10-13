<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Normalisation;

use Brotkrueml\MatomoIntegration\Normalisation\UrlNormaliser;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UrlNormaliser::class)]
final class UrlNormaliserTest extends TestCase
{
    #[Test]
    public function normalise(): void
    {
        $actual = UrlNormaliser::normalise('https://example.org');

        self::assertSame('https://example.org/', $actual);
    }
}
