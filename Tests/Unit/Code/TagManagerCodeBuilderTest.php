<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Code;

use Brotkrueml\MatomoIntegration\Code\TagManagerCodeBuilder;
use Brotkrueml\MatomoIntegration\Entity\Configuration;
use Brotkrueml\MatomoIntegration\Event\AddToDataLayerEvent;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

final class TagManagerCodeBuilderTest extends TestCase
{
    /**
     * @var Stub|EventDispatcherInterface
     */
    private $eventDispatcherStub;

    private TagManagerCodeBuilder $subject;

    protected function setUp(): void
    {
        $this->eventDispatcherStub = $this->createStub(EventDispatcherInterface::class);
        $this->subject = new TagManagerCodeBuilder($this->eventDispatcherStub);
    }

    /**
     * @test
     */
    public function getCodeReturnsTagManagerCodeCorrectly(): void
    {
        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturn(new AddToDataLayerEvent());

        $this->subject->setConfiguration(
            Configuration::createFromSiteConfiguration([
                'matomoIntegrationUrl' => 'https://www.example.net/',
                'matomoIntegrationSiteId' => 123,
                'matomoIntegrationTagManagerContainerId' => 'someId',
            ])
        );

        self::assertSame(
            'var _mtm=window._mtm||[];_mtm.push({"mtm.startTime":(new Date().getTime()),"event":"mtm.Start"});var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src="https://www.example.net/js/container_someId.js";s.parentNode.insertBefore(g,s);',
            $this->subject->getCode()
        );
    }

    /**
     * @test
     */
    public function getCodeReturnsTagManagerCodeWithEnabledDebugModeCorrectly(): void
    {
        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturn(new AddToDataLayerEvent());

        $this->subject->setConfiguration(
            Configuration::createFromSiteConfiguration([
                'matomoIntegrationUrl' => 'https://www.example.net/',
                'matomoIntegrationSiteId' => 123,
                'matomoIntegrationTagManagerContainerId' => 'someId',
                'matomoIntegrationTagManagerDebugMode' => true,
            ])
        );

        self::assertSame(
            'var _mtm=window._mtm||[];_mtm.push(["enableDebugMode"]);_mtm.push({"mtm.startTime":(new Date().getTime()),"event":"mtm.Start"});var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src="https://www.example.net/js/container_someId.js";s.parentNode.insertBefore(g,s);',
            $this->subject->getCode()
        );
    }

    /**
     * @test
     */
    public function getCodeReturnsTagManagerCodeWithDispatchedAddToDataLayerEventCorrectly(): void
    {
        $event = new AddToDataLayerEvent();
        $event->addVariable('someName', 'someValue');

        $this->eventDispatcherStub
            ->method('dispatch')
            ->willReturn($event);

        $this->subject->setConfiguration(
            Configuration::createFromSiteConfiguration([
                'matomoIntegrationUrl' => 'https://www.example.net/',
                'matomoIntegrationSiteId' => 123,
                'matomoIntegrationTagManagerContainerId' => 'someId',
            ])
        );

        self::assertSame(
            'var _mtm=window._mtm||[];_mtm.push({"mtm.startTime":(new Date().getTime()),"event":"mtm.Start","someName":"someValue"});var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src="https://www.example.net/js/container_someId.js";s.parentNode.insertBefore(g,s);',
            $this->subject->getCode()
        );
    }
}
