<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Event\BeforeTrackPageViewEvent;

use Brotkrueml\MatomoIntegration\Entity\CustomDimension;
use Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent;
use PHPUnit\Framework\TestCase;

final class EnrichTrackPageViewEventTest extends TestCase
{
    private EnrichTrackPageViewEvent $subject;

    protected function setUp(): void
    {
        $this->subject = new EnrichTrackPageViewEvent();
    }

    /**
     * @test
     */
    public function getPageTitleReturnsEmptyStringWhenNoPageTitleWasSet(): void
    {
        self::assertSame('', $this->subject->getPageTitle());
    }

    /**
     * @test
     */
    public function getPageTitleReturnsPreviouslySetPageTitle(): void
    {
        $this->subject->setPageTitle('some page title');

        self::assertSame('some page title', $this->subject->getPageTitle());
    }

    /**
     * @test
     */
    public function getCustomDimensionsReturnsEmptyArrayWhenNoCustomDimensionsWereAdded(): void
    {
        self::assertSame([], $this->subject->getCustomDimensions());
    }

    /**
     * @test
     */
    public function getCustomDimensionsReturnPreviouslyAddedCustomDimension(): void
    {
        $customDimension = new CustomDimension(42, 'some custom dimension value');

        $this->subject->addCustomDimension($customDimension);

        $actual = $this->subject->getCustomDimensions();

        self::assertCount(1, $actual);
        self::assertSame($customDimension, $actual[0]);
    }

    /**
     * @test
     */
    public function getCustomDimensionsReturnPreviouslyAddedCustomDimensions(): void
    {
        $customDimension1 = new CustomDimension(42, 'some custom dimension value');
        $customDimension2 = new CustomDimension(43, 'another custom dimension value');

        $this->subject->addCustomDimension($customDimension1);
        $this->subject->addCustomDimension($customDimension2);

        $actual = $this->subject->getCustomDimensions();

        self::assertCount(2, $actual);
        self::assertSame($customDimension1, $actual[0]);
        self::assertSame($customDimension2, $actual[1]);
    }
}
