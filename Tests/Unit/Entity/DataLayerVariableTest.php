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
use Brotkrueml\MatomoIntegration\Exceptions\InvalidDataLayerVariableValue;
use PHPUnit\Framework\TestCase;

final class DataLayerVariableTest extends TestCase
{
    /**
     * @test
     */
    public function getNameReturnsGivenName(): void
    {
        $subject = new DataLayerVariable('some name', '');

        self::assertSame('some name', $subject->getName());
    }

    /**
     * @test
     * @dataProvider dataProviderForValue
     */
    public function getValueReturnsGivenValueForDifferentTypes($value): void
    {
        $subject = new DataLayerVariable('some name', $value);

        self::assertSame($value, $subject->getValue());
    }

    public function dataProviderForValue(): iterable
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

    /**
     * @test
     */
    public function invalidValueTypeGivenThrowsException(): void
    {
        $this->expectException(InvalidDataLayerVariableValue::class);
        $this->expectExceptionCode(1629652718);
        $this->expectExceptionMessage('A data layer value with the invalid type "array" was given, allowed: int, float, string, Brotkrueml\\MatomoIntegration\\Code\\JavaScriptCode');

        /** @noinspection PhpParamsInspection */
        new DataLayerVariable('some name', []);
    }
}
