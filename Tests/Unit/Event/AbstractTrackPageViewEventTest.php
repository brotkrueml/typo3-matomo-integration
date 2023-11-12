<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Event;

use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Event\AbstractTrackPageViewEvent;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class AbstractTrackPageViewEventTest extends TestCase
{
    /**
     * @var Stub&ServerRequestInterface
     */
    private Stub $requestStub;
    private AbstractTrackPageViewEvent $subject;

    protected function setUp(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.net/',
            'matomoIntegrationSiteId' => 123,
        ]);

        $this->requestStub = $this->createStub(ServerRequestInterface::class);

        $this->subject = new class($configuration, $this->requestStub) extends AbstractTrackPageViewEvent {
        };
    }

    /**
     * @test
     */
    public function getRequestReturnsRequestObjectCorrectly(): void
    {
        self::assertSame($this->requestStub, $this->subject->getRequest());
    }

    /**
     * @test
     */
    public function getJavaScriptCodesReturnsEmptyArrayIfNoCodeWasAdded(): void
    {
        self::assertSame([], $this->subject->getJavaScriptCodes());
    }

    /**
     * @test
     */
    public function getMatomoMethodCallsReturnsEmptyArrayIfNoCallWasAdded(): void
    {
        self::assertSame([], $this->subject->getMatomoMethodCalls());
    }

    /**
     * @test
     */
    public function getJavaScriptCodesReturnsCodeCorrectlyIfOneJavaScriptCodeWasAdded(): void
    {
        $this->subject->addJavaScriptCode('/* some code */');

        $actual = $this->subject->getJavaScriptCodes();

        self::assertCount(1, $actual);
        self::assertSame('/* some code */', (string)$actual[0]);
    }

    /**
     * @test
     */
    public function getJavaScriptCodesReturnsCodesCorrectlyIfTwoJavaScriptCodesWereAdded(): void
    {
        $this->subject->addJavaScriptCode('/* some code */');
        $this->subject->addJavaScriptCode('/* another code */');

        $actual = $this->subject->getJavaScriptCodes();

        self::assertCount(2, $actual);
        self::assertSame('/* some code */', (string)$actual[0]);
        self::assertSame('/* another code */', (string)$actual[1]);
    }

    /**
     * @test
     */
    public function getMatomoMethodCallsReturnsCallsCorrectlyIfOneMatomoMethodCallWasAdded(): void
    {
        $this->subject->addMatomoMethodCall('someMethodCall');

        $actual = $this->subject->getMatomoMethodCalls();

        self::assertCount(1, $actual);
        self::assertSame('_paq.push(["someMethodCall"]);', (string)$actual[0]);
    }

    /**
     * @test
     */
    public function getCodeReturnsCodeCorrectlyIfTwoMatomoMethodCallsWereAdded(): void
    {
        $this->subject->addMatomoMethodCall('someMethodCall');
        $this->subject->addMatomoMethodCall('anotherMethodCall');

        $actual = $this->subject->getMatomoMethodCalls();

        self::assertCount(2, $actual);
        self::assertSame('_paq.push(["someMethodCall"]);', (string)$actual[0]);
        self::assertSame('_paq.push(["anotherMethodCall"]);', (string)$actual[1]);
    }
}
