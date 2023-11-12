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
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

final class TagManagerCodeBuilderTest extends TestCase
{
    /**
     * @var Stub&ServerRequestInterface
     */
    private Stub $requestStub;
    private TagManagerCodeBuilder $subject;

    protected function setUp(): void
    {
        $eventDispatcher = new class() implements EventDispatcherInterface {
            public function dispatch(object $event, string $eventName = null): object
            {
                return $event;
            }
        };
        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->subject = new TagManagerCodeBuilder($eventDispatcher);
    }

    /**
     * @test
     */
    public function getCodeReturnsTagManagerCodeCorrectly(): void
    {
        $this->subject
            ->setRequest($this->requestStub)
            ->setConfiguration(
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
        $this->subject
            ->setRequest($this->requestStub)
            ->setConfiguration(
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
        $eventDispatcher = new class() implements EventDispatcherInterface {
            public function dispatch(object $event, string $eventName = null): object
            {
                $event->addVariable('someName', 'someValue');

                return $event;
            }
        };

        $subject = new TagManagerCodeBuilder($eventDispatcher);
        $subject
            ->setRequest($this->requestStub)
            ->setConfiguration(
                Configuration::createFromSiteConfiguration([
                    'matomoIntegrationUrl' => 'https://www.example.net/',
                    'matomoIntegrationSiteId' => 123,
                    'matomoIntegrationTagManagerContainerId' => 'someId',
                ])
            );

        self::assertSame(
            'var _mtm=window._mtm||[];_mtm.push({"mtm.startTime":(new Date().getTime()),"event":"mtm.Start","someName":"someValue"});var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src="https://www.example.net/js/container_someId.js";s.parentNode.insertBefore(g,s);',
            $subject->getCode()
        );
    }

    /**
     * @test
     */
    public function getCodeReturnsTagManagerCodeWithMultipleContainerIdsCorrectly(): void
    {
        $this->subject
            ->setRequest($this->requestStub)
            ->setConfiguration(
                Configuration::createFromSiteConfiguration([
                    'matomoIntegrationUrl' => 'https://www.example.net/',
                    'matomoIntegrationSiteId' => 123,
                    'matomoIntegrationTagManagerContainerId' => 'someId,anotherId',
                ])
            );

        self::assertSame(
            'var _mtm=window._mtm||[];_mtm.push({"mtm.startTime":(new Date().getTime()),"event":"mtm.Start"});var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src="https://www.example.net/js/container_someId.js";s.parentNode.insertBefore(g,s);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src="https://www.example.net/js/container_anotherId.js";s.parentNode.insertBefore(g,s);',
            $this->subject->getCode()
        );
    }
}
