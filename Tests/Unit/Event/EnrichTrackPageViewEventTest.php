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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(EnrichTrackPageViewEvent::class)]
final class EnrichTrackPageViewEventTest extends TestCase
{
    /**
     * @var Stub&ServerRequestInterface
     */
    private Stub $requestStub;
    private EnrichTrackPageViewEvent $subject;

    protected function setUp(): void
    {
        $this->requestStub = self::createStub(ServerRequestInterface::class);
        $this->subject = new EnrichTrackPageViewEvent($this->requestStub);
    }

    #[Test]
    public function getRequestReturnsRequestObjectCorrectly(): void
    {
        self::assertSame($this->requestStub, $this->subject->getRequest());
    }

    #[Test]
    public function getPageTitleReturnsEmptyStringWhenNoPageTitleWasSet(): void
    {
        self::assertSame('', $this->subject->getPageTitle());
    }

    #[Test]
    public function getPageTitleReturnsPreviouslySetPageTitle(): void
    {
        $this->subject->setPageTitle('some page title');

        self::assertSame('some page title', $this->subject->getPageTitle());
    }

    #[Test]
    public function getCustomDimensionsReturnsEmptyArrayWhenNoCustomDimensionsWereAdded(): void
    {
        self::assertSame([], $this->subject->getCustomDimensions());
    }

    #[Test]
    public function getCustomDimensionsReturnPreviouslyAddedCustomDimension(): void
    {
        $this->subject->addCustomDimension(42, 'some custom dimension value');

        $actual = $this->subject->getCustomDimensions();

        self::assertCount(1, $actual);
        self::assertSame(42, $actual[0]->id);
        self::assertSame('some custom dimension value', $actual[0]->value);
    }

    #[Test]
    public function getCustomDimensionsReturnPreviouslyAddedCustomDimensions(): void
    {
        $this->subject->addCustomDimension(42, 'some custom dimension value');
        $this->subject->addCustomDimension(43, 'another custom dimension value');

        $actual = $this->subject->getCustomDimensions();

        self::assertCount(2, $actual);
        self::assertSame(42, $actual[0]->id);
        self::assertSame('some custom dimension value', $actual[0]->value);
        self::assertSame(43, $actual[1]->id);
        self::assertSame('another custom dimension value', $actual[1]->value);
    }
}
