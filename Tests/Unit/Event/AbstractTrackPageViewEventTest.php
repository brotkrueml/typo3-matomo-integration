<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Event\BeforeTrackPageViewEvent;

use Brotkrueml\MatomoIntegration\Entity\JavaScriptCode;
use Brotkrueml\MatomoIntegration\Entity\MatomoMethodCall;
use Brotkrueml\MatomoIntegration\Event\AbstractTrackPageViewEvent;
use PHPUnit\Framework\TestCase;

final class AbstractTrackPageViewEventTest extends TestCase
{
    private AbstractTrackPageViewEvent $subject;

    protected function setUp(): void
    {
        $this->subject = new class() extends AbstractTrackPageViewEvent {
        };
    }

    /**
     * @test
     */
    public function getCodeReturnsEmptyStringIfNoCodeWasAdded(): void
    {
        self::assertSame('', $this->subject->getCode());
    }

    /**
     * @test
     */
    public function getCodeReturnsCodeCorrectlyIfOneJavaScriptCodeWasAdded(): void
    {
        $this->subject->addJavaScriptCode(new JavaScriptCode('/* some code */'));

        self::assertSame('/* some code */', $this->subject->getCode());
    }

    /**
     * @test
     */
    public function getCodeReturnsCodeCorrectlyIfTwoJavaScriptCodesWereAdded(): void
    {
        $this->subject->addJavaScriptCode(new JavaScriptCode('/* some code */'));
        $this->subject->addJavaScriptCode(new JavaScriptCode('/* another code */'));

        self::assertSame('/* some code *//* another code */', $this->subject->getCode());
    }

    /**
     * @test
     */
    public function getCodeReturnsCodeCorrectlyIfOneMatomoMethodCallWasAdded(): void
    {
        $this->subject->addMatomoMethodCall(new MatomoMethodCall('someMethodCall'));

        self::assertSame('_paq.push(["someMethodCall"]);', $this->subject->getCode());
    }

    /**
     * @test
     */
    public function getCodeReturnsCodeCorrectlyIfTwoMatomoMethodCallsWereAdded(): void
    {
        $this->subject->addMatomoMethodCall(new MatomoMethodCall('someMethodCall'));
        $this->subject->addMatomoMethodCall(new MatomoMethodCall('anotherMethodCall'));

        self::assertSame('_paq.push(["someMethodCall"]);_paq.push(["anotherMethodCall"]);', $this->subject->getCode());
    }

    /**
     * @test
     */
    public function getCodeReturnsJavaScriptCodeBeforeMatomoMethodCall(): void
    {
        $this->subject->addJavaScriptCode(new JavaScriptCode('/* some code */'));
        $this->subject->addMatomoMethodCall(new MatomoMethodCall('someMethodCall'));

        self::assertSame('/* some code */_paq.push(["someMethodCall"]);', $this->subject->getCode());
    }
}