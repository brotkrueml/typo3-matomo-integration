<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Event;

use Brotkrueml\MatomoIntegration\Event\EnrichScriptTagEvent;
use Brotkrueml\MatomoIntegration\Exceptions\InvalidDataAttributeName;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

final class EnrichScriptTagEventTest extends TestCase
{
    /**
     * @var Stub&Site
     */
    private Stub $siteStub;

    /**
     * @var Stub&ServerRequestInterface
     */
    private Stub $requestStub;

    protected function setUp(): void
    {
        $this->siteStub = $this->createStub(Site::class);

        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->requestStub
            ->method('getAttribute')
            ->with('site')
            ->willReturn($this->siteStub);
    }

    /**
     * @test
     */
    public function setAndGetIdReturnsTheCorrectValue(): void
    {
        $event = new EnrichScriptTagEvent($this->requestStub);
        $event->setId('example');
        self::assertSame('example', $event->getId());
    }

    /**
     * @test
     */
    public function setAndGetForTypeReturnsTheCorrectValue(): void
    {
        $event = new EnrichScriptTagEvent($this->requestStub);
        $event->setType('example');
        self::assertSame('example', $event->getType());
    }

    /**
     * @test
     */
    public function addAndGetDataAttributesReturnsTheCorrectValue(): void
    {
        $event = new EnrichScriptTagEvent($this->requestStub);
        $event->addDataAttribute('foo', 'bar');
        $event->addDataAttribute('qux');

        self::assertCount(2, $event->getDataAttributes());
        self::assertSame([
            'foo' => 'bar',
            'qux' => '',
        ], $event->getDataAttributes());
    }

    /**
     * @test
     * @dataProvider dataProviderToTestExceptions
     */
    public function wrongCharactersInAttributeNameLeadsToAnException(
        string $name,
        string $value,
        int $code,
    ): void {
        $this->expectException(InvalidDataAttributeName::class);
        $this->expectExceptionCode($code);
        $event = new EnrichScriptTagEvent($this->requestStub);
        $event->addDataAttribute($name, $value);
    }

    public function dataProviderToTestExceptions(): iterable
    {
        yield 'Attribute name should not contain/not start with data-' => [
            'name' => 'data-answer',
            'value' => '42',
            'code' => 1644869412,
        ];
        yield 'Attribute name should not contains a blank' => [
            'name' => 'ans wer',
            'value' => '42',
            'code' => 1644869542,
        ];
        yield 'Attribute name should not contains a equal sign' => [
            'name' => 'ans=wer',
            'value' => '42',
            'code' => 1644869542,
        ];
        yield 'Attribute name should not contains a single quote' => [
            'name' => '\'',
            'value' => '42',
            'code' => 1644869542,
        ];
        yield 'Attribute name should not contains a double quote' => [
            'name' => '"',
            'value' => '42',
            'code' => 1644869542,
        ];
        yield 'Attribute name should not contains a closed pointed bracket' => [
            'name' => '>',
            'value' => '42',
            'code' => 1644869542,
        ];
        yield 'Attribute name should not contains a backslash' => [
            'name' => '\\',
            'value' => '42',
            'code' => 1644869542,
        ];
    }
}
