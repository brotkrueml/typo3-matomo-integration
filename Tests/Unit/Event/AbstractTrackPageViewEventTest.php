<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Event;

use Brotkrueml\MatomoIntegration\Code\JavaScriptCode;
use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Event\AbstractTrackPageViewEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(AbstractTrackPageViewEvent::class)]
final class AbstractTrackPageViewEventTest extends TestCase
{
    private Configuration $configuration;
    private Stub&ServerRequestInterface $requestStub;
    private AbstractTrackPageViewEvent $subject;

    protected function setUp(): void
    {
        $this->configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
        ]);

        $this->requestStub = self::createStub(ServerRequestInterface::class);

        $this->subject = new class($this->configuration, $this->requestStub) extends AbstractTrackPageViewEvent {};
    }

    #[Test]
    public function getConfigurationReturnsConfigurationCorrectly(): void
    {
        self::assertSame($this->configuration, $this->subject->getConfiguration());
    }

    #[Test]
    public function getRequestReturnsRequestObjectCorrectly(): void
    {
        self::assertSame($this->requestStub, $this->subject->getRequest());
    }

    #[Test]
    public function getJavaScriptCodesReturnsEmptyArrayIfNoCodeWasAdded(): void
    {
        self::assertSame([], $this->subject->getJavaScriptCodes());
    }

    #[Test]
    public function getMatomoMethodCallsReturnsEmptyArrayIfNoCallWasAdded(): void
    {
        self::assertSame([], $this->subject->getMatomoMethodCalls());
    }

    #[Test]
    public function getJavaScriptCodesReturnsCodeCorrectlyIfOneJavaScriptCodeWasAdded(): void
    {
        $this->subject->addJavaScriptCode('/* some code */');

        $actual = $this->subject->getJavaScriptCodes();

        self::assertCount(1, $actual);
        self::assertSame('/* some code */', (string) $actual[0]);
    }

    #[Test]
    public function getJavaScriptCodesReturnsCodesCorrectlyIfTwoJavaScriptCodesWereAdded(): void
    {
        $this->subject->addJavaScriptCode('/* some code */');
        $this->subject->addJavaScriptCode('/* another code */');

        $actual = $this->subject->getJavaScriptCodes();

        self::assertCount(2, $actual);
        self::assertSame('/* some code */', (string) $actual[0]);
        self::assertSame('/* another code */', (string) $actual[1]);
    }

    #[Test]
    public function getMatomoMethodCallsReturnsCallsCorrectlyIfOneMatomoMethodCallWasAdded(): void
    {
        $this->subject->addMatomoMethodCall('someMethodCall');

        $actual = $this->subject->getMatomoMethodCalls();

        self::assertCount(1, $actual);
        self::assertSame('_paq.push(["someMethodCall"]);', (string) $actual[0]);
    }

    #[Test]
    public function getMatomoMethodCallsReturnsCodeCorrectlyIfTwoMatomoMethodCallsWereAdded(): void
    {
        $this->subject->addMatomoMethodCall('someMethodCall');
        $this->subject->addMatomoMethodCall('anotherMethodCall');

        $actual = $this->subject->getMatomoMethodCalls();

        self::assertCount(2, $actual);
        self::assertSame('_paq.push(["someMethodCall"]);', (string) $actual[0]);
        self::assertSame('_paq.push(["anotherMethodCall"]);', (string) $actual[1]);
    }

    #[Test]
    #[DataProvider('providerForDifferentParameterTypes')]
    public function getMatomoMethodCallsReturnsCodeCorrectlyForDifferentParameterTypes(mixed $parameter, string $expected): void
    {
        $this->subject->addMatomoMethodCall('someMethodCall', $parameter);

        $actual = $this->subject->getMatomoMethodCalls();

        self::assertSame('_paq.push(["someMethodCall",' . $expected . ']);', (string) $actual[0]);
    }

    public static function providerForDifferentParameterTypes(): iterable
    {
        yield 'with array' => [
            'parameter' => [
                'foo' => 'bar',
            ],
            'expected' => '["bar"]',
        ];

        yield 'with bool' => [
            'parameter' => true,
            'expected' => 'true',
        ];

        yield 'with int' => [
            'parameter' => 42,
            'expected' => '42',
        ];

        yield 'with float' => [
            'parameter' => 42.123,
            'expected' => '42.123',
        ];

        yield 'with string' => [
            'parameter' => 'foobar',
            'expected' => '"foobar"',
        ];

        yield 'with JavaScriptCode' => [
            'parameter' => new JavaScriptCode('foobar()'),
            'expected' => 'foobar()',
        ];
    }

    #[Test]
    public function getMatomoMethodCallsReturnsCodeCorrectlyForMultipleParameters(): void
    {
        $this->subject->addMatomoMethodCall(
            'trackEcommerceOrder',
            '000123',
            10.99,
            9.99,
            1.5,
            1,
            false,
        );

        $actual = $this->subject->getMatomoMethodCalls();

        self::assertSame('_paq.push(["trackEcommerceOrder","000123",10.99,9.99,1.5,1,false]);', (string) $actual[0]);
    }
}
