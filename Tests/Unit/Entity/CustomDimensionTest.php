<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Entity;

use Brotkrueml\MatomoIntegration\Entity\CustomDimension;
use Brotkrueml\MatomoIntegration\Exceptions\InvalidCustomDimensionId;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CustomDimensionTest extends TestCase
{
    #[Test]
    public function invalidGivenIdThrowsException(): void
    {
        $this->expectException(InvalidCustomDimensionId::class);
        $this->expectExceptionCode(1628782795);
        $this->expectExceptionMessage('The id for a custom dimension has to be a positive integer, "0" given');

        new CustomDimension(0, 'some value');
    }

    #[Test]
    public function givenValuesAreReturnedCorrectlyViaGetters(): void
    {
        $subject = new CustomDimension(1, 'some value');

        self::assertSame(1, $subject->id);
        self::assertSame('some value', $subject->value);
    }
}
