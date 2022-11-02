<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Event;

use Brotkrueml\MatomoIntegration\Event\AddToDataLayerEvent;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class AddToDataLayerEventTest extends TestCase
{
    /**
     * @var Stub&ServerRequestInterface
     */
    private $requestStub;
    private AddToDataLayerEvent $subject;

    protected function setUp(): void
    {
        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->subject = new AddToDataLayerEvent($this->requestStub);
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
    public function getVariablesReturnsEmptyArrayWhenNoVariablesWereAdded(): void
    {
        self::assertSame([], $this->subject->getVariables());
    }

    /**
     * @test
     */
    public function getVariablesReturnsPreviouslyAddedVariables(): void
    {
        $this->subject->addVariable('orderTotal', 4545.45);
        $this->subject->addVariable('orderCurrency', 'EUR');

        $actual = $this->subject->getVariables();

        self::assertCount(2, $actual);
        self::assertSame('orderTotal', $actual[0]->getName());
        self::assertSame(4545.45, $actual[0]->getValue());
        self::assertSame('orderCurrency', $actual[1]->getName());
        self::assertSame('EUR', $actual[1]->getValue());
    }
}
