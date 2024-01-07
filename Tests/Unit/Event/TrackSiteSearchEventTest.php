<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Event;

use Brotkrueml\MatomoIntegration\Entity\CustomDimension;
use Brotkrueml\MatomoIntegration\Event\TrackSiteSearchEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class TrackSiteSearchEventTest extends TestCase
{
    /**
     * @var Stub&ServerRequestInterface
     */
    private Stub $requestStub;
    private TrackSiteSearchEvent $subject;

    protected function setUp(): void
    {
        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->subject = new TrackSiteSearchEvent($this->requestStub);
    }

    #[Test]
    public function getRequestReturnsRequestObjectCorrectly(): void
    {
        self::assertSame($this->requestStub, $this->subject->getRequest());
    }

    #[Test]
    public function getKeywordReturnsEmptyStringWhenNoKeywordWasSet(): void
    {
        self::assertSame('', $this->subject->getKeyword());
    }

    #[Test]
    public function getKeywordReturnsPreviouslySetKeyword(): void
    {
        $this->subject->setKeyword('some keyword');

        self::assertSame('some keyword', $this->subject->getKeyword());
    }

    #[Test]
    public function getCategoryReturnsFalseWhenNoCategoryWasSet(): void
    {
        self::assertFalse($this->subject->getCategory());
    }

    #[Test]
    public function getCategoryReturnsPreviouslySetCategory(): void
    {
        $this->subject->setCategory('some category');

        self::assertSame('some category', $this->subject->getCategory());
    }

    #[Test]
    public function getSearchCountReturnsFalseWhenNoSearchCountWasSet(): void
    {
        self::assertFalse($this->subject->getSearchCount());
    }

    #[Test]
    public function getSearchCountReturnsPreviouslySetSearchCount(): void
    {
        $this->subject->setSearchCount(42);

        self::assertSame(42, $this->subject->getSearchCount());
    }

    #[Test]
    public function getCustomDimensionsReturnsEmptyArrayWhenNoCustomDimensionsWasSet(): void
    {
        self::assertSame([], $this->subject->getCustomDimensions());
    }

    #[Test]
    public function getCustomDimensionsReturnsPreviouslySetOneCustomDimension(): void
    {
        $this->subject->addCustomDimension(1, 'some custom dimension');

        self::assertCount(1, $this->subject->getCustomDimensions());
        self::assertInstanceOf(CustomDimension::class, $this->subject->getCustomDimensions()[0]);
        self::assertSame(1, $this->subject->getCustomDimensions()[0]->id);
        self::assertSame('some custom dimension', $this->subject->getCustomDimensions()[0]->value);
    }

    #[Test]
    public function getCustomDimensionsReturnsPreviouslySetTwoCustomDimensions(): void
    {
        $this->subject->addCustomDimension(1, 'some custom dimension');
        $this->subject->addCustomDimension(2, 'another custom dimension');

        self::assertCount(2, $this->subject->getCustomDimensions());
        self::assertInstanceOf(CustomDimension::class, $this->subject->getCustomDimensions()[0]);
        self::assertSame(1, $this->subject->getCustomDimensions()[0]->id);
        self::assertSame('some custom dimension', $this->subject->getCustomDimensions()[0]->value);
        self::assertInstanceOf(CustomDimension::class, $this->subject->getCustomDimensions()[1]);
        self::assertSame(2, $this->subject->getCustomDimensions()[1]->id);
        self::assertSame('another custom dimension', $this->subject->getCustomDimensions()[1]->value);
    }
}
