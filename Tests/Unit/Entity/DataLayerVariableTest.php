<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Entity;

use Brotkrueml\MatomoIntegration\Code\JavaScriptCode;
use Brotkrueml\MatomoIntegration\Entity\DataLayerVariable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DataLayerVariableTest extends TestCase
{
    #[Test]
    public function getNameReturnsGivenName(): void
    {
        $subject = new DataLayerVariable('some name', '');

        self::assertSame('some name', $subject->getName());
    }

    #[Test]
    #[DataProvider('dataProviderForValue')]
    public function getValueReturnsGivenValueForDifferentTypes(string|int|float|JavaScriptCode $value): void
    {
        $subject = new DataLayerVariable('some name', $value);

        self::assertSame($value, $subject->getValue());
    }

    public static function dataProviderForValue(): iterable
    {
        yield 'value is a string' => [
            'some value',
        ];

        yield 'value is an integer' => [
            42,
        ];

        yield 'value is a float' => [
            3.14159,
        ];

        yield 'value is JavaScriptCode' => [
            new JavaScriptCode('/* some code */'),
        ];
    }
}
