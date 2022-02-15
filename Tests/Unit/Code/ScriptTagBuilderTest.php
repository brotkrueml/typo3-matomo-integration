<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Code;

use Brotkrueml\MatomoIntegration\Code\ScriptTagBuilder;
use Brotkrueml\MatomoIntegration\Event\EnrichScriptTagEvent;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * This test check if the ScriptTagBuilder works as expected
 */
final class ScriptTagBuilderTest extends TestCase
{
    /**
     * @var Stub|Site
     */
    private $siteStub;

    /**
     * @var Stub|ServerRequestInterface
     */
    private $requestStub;

    protected function setUp(): void
    {
        $this->siteStub = $this->createStub(Site::class);

        $this->requestStub = $this->createStub(ServerRequestInterface::class);
        $this->requestStub
            ->method('getAttribute')
            ->with('site')
            ->willReturn($this->siteStub);
    }

    /**
     * @test
     * @dataProvider dataProviderForAttributeTests
     */
    public function enrichScriptTagViaEventAddsIdTypeAndDataAttributes(
        string $id,
        string $type,
        array $data,
        string $expected
    ): void {
        $enrichEvent = new EnrichScriptTagEvent($this->requestStub);
        $enrichEvent->setId($id);
        $enrichEvent->setType($type);
        foreach ($data as $name => $value) {
            $enrichEvent->addDataAttribute($name, $value);
        }

        $eventDispatcherStub = $this->createStub(EventDispatcherInterface::class);
        $eventDispatcherStub
            ->method('dispatch')
            ->willReturn($enrichEvent);

        $scriptTagBuilder = new ScriptTagBuilder(
            $eventDispatcherStub
        );
        $scriptTagBuilder->setRequest($this->requestStub);

        self::assertSame(
            $expected,
            $scriptTagBuilder->build('/* some tracking code */')
        );
    }

    public function dataProviderForAttributeTests(): iterable
    {
        yield 'If no id nor type nor data is set, a tag without attributes is rendered' => [
            'id' => '',
            'type' => '',
            'data' => [],
            'expected' => '<script>/* some tracking code */</script>',
        ];

        yield 'Id with a value is given' => [
            'id' => 'example',
            'type' => '',
            'data' => [],
            'expected' => '<script id="example">/* some tracking code */</script>',
        ];

        yield 'Type with a value is given' => [
            'id' => '',
            'type' => 'example',
            'data' => [],
            'expected' => '<script type="example">/* some tracking code */</script>',
        ];

        yield 'Id and type with value are given' => [
            'id' => 'example',
            'type' => 'example',
            'data' => [],
            'expected' => '<script id="example" type="example">/* some tracking code */</script>',
        ];

        yield 'Data attribute without a value is given' => [
            'id' => '',
            'type' => '',
            'data' => [
                'question' => '',
            ],
            'expected' => '<script data-question>/* some tracking code */</script>',
        ];

        yield 'Data attribute with a value is given' => [
            'id' => '',
            'type' => '',
            'data' => [
                'answer' => '42',
            ],
            'expected' => '<script data-answer="42">/* some tracking code */</script>',
        ];

        yield 'Data attributes with and without a value are given' => [
            'id' => '',
            'type' => '',
            'data' => [
                'question' => '',
                'answer' => '42',
            ],
            'expected' => '<script data-question data-answer="42">/* some tracking code */</script>',
        ];

        yield 'Id, type and data attributes with and without a value are given' => [
            'id' => 'example',
            'type' => 'example',
            'data' => [
                'question' => '',
                'answer' => '42',
            ],
            'expected' => '<script id="example" type="example" data-question data-answer="42">/* some tracking code */</script>',
        ];
    }
}
