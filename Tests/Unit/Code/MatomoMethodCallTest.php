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
use Brotkrueml\MatomoIntegration\Code\MatomoMethodCall;
use Brotkrueml\MatomoIntegration\Exceptions\InvalidMatomoMethodName;
use Brotkrueml\MatomoIntegration\Exceptions\InvalidMatomoMethodParameter;
use PHPUnit\Framework\TestCase;

final class MatomoMethodCallTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataProvider
     */
    public function toStringReturnsMethodCallCorrectly(string $method, array $arguments, string $expected): void
    {
        $subject = new MatomoMethodCall($method, ...$arguments);

        self::assertSame($expected, (string)$subject);
    }

    public function dataProvider(): iterable
    {
        yield 'Only method given' => [
            'someMethodName',
            [],
            '_paq.push(["someMethodName"]);',
        ];

        yield 'Method with int parameter given' => [
            'someMethodName',
            [42],
            '_paq.push(["someMethodName",42]);',
        ];

        yield 'Method with string parameter given' => [
            'someMethodName',
            ['some value'],
            '_paq.push(["someMethodName","some value"]);',
        ];

        yield 'Method with string parameter and double quotes given' => [
            'someMethodName',
            ['some "value"'],
            '_paq.push(["someMethodName","some \"value\""]);',
        ];

        yield 'Method with JSON given' => [
            'someMethodName',
            ['{"key":"value"}'],
            '_paq.push(["someMethodName",{"key":"value"}]);',
        ];

        yield 'Method with bool parameter "true" given' => [
            'someMethodName',
            [true],
            '_paq.push(["someMethodName",true]);',
        ];

        yield 'Method with bool parameter "false" given' => [
            'someMethodName',
            [false],
            '_paq.push(["someMethodName",false]);',
        ];

        yield 'Method with array parameter given' => [
            'someMethodName',
            [['value "1"', 2, true]],
            '_paq.push(["someMethodName",["value \"1\"",2,true]]);',
        ];

        yield 'Method with JavaScriptCode parameter given' => [
            'someMethodName',
            [new JavaScriptCode('someJavaScriptMethod()')],
            '_paq.push(["someMethodName",someJavaScriptMethod()]);',
        ];

        yield 'Method with multiple parameters given' => [
            'someMethodName',
            ['some string', 42, false, new JavaScriptCode('someJavaScriptVariable')],
            '_paq.push(["someMethodName","some string",42,false,someJavaScriptVariable]);',
        ];
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenInvalidMethodNameIsGiven(): void
    {
        $this->expectException(InvalidMatomoMethodName::class);
        $this->expectExceptionCode(1629212630);
        $this->expectExceptionMessage('The given Matomo method name "some method name" is not valid, only characters between a and z are allowed!');

        new MatomoMethodCall('some method name');
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenInvalidParameterTypeIsGiven(): void
    {
        $this->expectException(InvalidMatomoMethodParameter::class);
        $this->expectExceptionCode(1629212630);
        $this->expectExceptionMessage('A Matomo method argument with the invalid type "null" was given, allowed: array, bool, int, string, Brotkrueml\\MatomoIntegration\\Code\\JavaScriptCode');

        (new MatomoMethodCall('someMethodName', null))->__toString();
    }
}
