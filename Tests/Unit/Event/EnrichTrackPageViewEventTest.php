<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Event;

use Brotkrueml\MatomoIntegration\Event\EnrichTrackPageViewEvent;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class EnrichTrackPageViewEventTest extends TestCase
{
    /**
     * @var Stub&ServerRequestInterface
     */
    private $requestStub;
    private EnrichTrackPageViewEvent $subject;

    protected function setUp(): void
    {
        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->subject = new EnrichTrackPageViewEvent($this->requestStub);
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
        $this->subject->addCustomDimension(42, 'some custom dimension value');

        $actual = $this->subject->getCustomDimensions();

        self::assertCount(1, $actual);
        self::assertSame(42, $actual[0]->getId());
        self::assertSame('some custom dimension value', $actual[0]->getValue());
    }

    /**
     * @test
     */
    public function getCustomDimensionsReturnPreviouslyAddedCustomDimensions(): void
    {
        $this->subject->addCustomDimension(42, 'some custom dimension value');
        $this->subject->addCustomDimension(43, 'another custom dimension value');

        $actual = $this->subject->getCustomDimensions();

        self::assertCount(2, $actual);
        self::assertSame(42, $actual[0]->getId());
        self::assertSame('some custom dimension value', $actual[0]->getValue());
        self::assertSame(43, $actual[1]->getId());
        self::assertSame('another custom dimension value', $actual[1]->getValue());
    }
}
