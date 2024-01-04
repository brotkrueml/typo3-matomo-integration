<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\JavaScript;

use Brotkrueml\MatomoIntegration\Code\JavaScriptCode;
use Brotkrueml\MatomoIntegration\JavaScript\JavaScriptObjectPairCollector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class JavaScriptObjectPairCollectorTest extends TestCase
{
    private JavaScriptObjectPairCollector $subject;

    protected function setUp(): void
    {
        $this->subject = new JavaScriptObjectPairCollector();
    }

    #[Test]
    #[DataProvider('dataProvider')]
    public function toStringReturnsEmptyObjectIfNoPairsAreAvailable(array $pairs, string $expected): void
    {
        foreach ($pairs as $pair) {
            $this->subject->addPair($pair['name'], $pair['value']);
        }

        self::assertSame($expected, $this->subject->__toString());
    }

    public static function dataProvider(): iterable
    {
        yield 'No pairs are available' => [
            'pairs' => [],
            'expected' => '{}',
        ];

        yield 'One pair with string as a value' => [
            'pairs' => [
                [
                    'name' => 'some name',
                    'value' => 'some value',
                ],
            ],
            'expected' => '{"some name":"some value"}',
        ];

        yield 'One pair with int as a value' => [
            'pairs' => [
                [
                    'name' => 'some name',
                    'value' => 42,
                ],
            ],
            'expected' => '{"some name":42}',
        ];

        yield 'One pair with float as a value' => [
            'pairs' => [
                [
                    'name' => 'some name',
                    'value' => 3.14159,
                ],
            ],
            'expected' => '{"some name":3.14159}',
        ];

        yield 'One pair with JavaScriptCode as a value' => [
            'pairs' => [
                [
                    'name' => 'some name',
                    'value' => new JavaScriptCode('someFunctionCall()'),
                ],
            ],
            'expected' => '{"some name":someFunctionCall()}',
        ];

        yield 'Two pairs with different value types' => [
            'pairs' => [
                [
                    'name' => 'some name',
                    'value' => 'some value',
                ],
                [
                    'name' => 'another name',
                    'value' => new JavaScriptCode('someFunctionCall()'),
                ],
            ],
            'expected' => '{"some name":"some value","another name":someFunctionCall()}',
        ];

        yield 'Two pairs with the same name' => [
            'pairs' => [
                [
                    'name' => 'some name',
                    'value' => 'some value',
                ],
                [
                    'name' => 'some name',
                    'value' => 'another value',
                ],
            ],
            'expected' => '{"some name":"another value"}',
        ];

        yield 'Double quote in name is encoded' => [
            'pairs' => [
                [
                    'name' => 'some "name"',
                    'value' => 'some value',
                ],
            ],
            'expected' => '{"some \"name\"":"some value"}',
        ];

        yield 'Double quote in value is encoded' => [
            'pairs' => [
                [
                    'name' => 'some name',
                    'value' => 'some "value"',
                ],
            ],
            'expected' => '{"some name":"some \"value\""}',
        ];
    }
}
