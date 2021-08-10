<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Event\BeforeTrackPageViewEvent;

use Brotkrueml\MatomoIntegration\Event\BeforeTrackPageViewEvent;
use PHPUnit\Framework\TestCase;

final class BeforeTrackPageViewEventTest extends TestCase
{
    private BeforeTrackPageViewEvent $subject;

    protected function setUp(): void
    {
        $this->subject = new BeforeTrackPageViewEvent();
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
    public function getCodeReturnsCodeCorrectlyIfOneCodeWasAdded(): void
    {
        $this->subject->addCode('/* some code */');

        self::assertSame('/* some code */', $this->subject->getCode());
    }

    /**
     * @test
     */
    public function getCodeReturnsCodeCorrectlyIfTwoCodesWereAdded(): void
    {
        $this->subject->addCode('/* some code */');
        $this->subject->addCode('/* another code */');

        self::assertSame('/* some code *//* another code */', $this->subject->getCode());
    }
}
