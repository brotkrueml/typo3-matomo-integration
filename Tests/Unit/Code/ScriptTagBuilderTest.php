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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Domain\ConsumableString;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Site\Entity\Site;

#[CoversClass(ScriptTagBuilder::class)]
final class ScriptTagBuilderTest extends TestCase
{
    /**
     * @var Stub&Site
     */
    private Stub $siteStub;

    /**
     * @var Stub&ServerRequestInterface
     */
    private Stub $requestStub;

    protected function setUp(): void
    {
        $this->siteStub = self::createStub(Site::class);

        $this->requestStub = self::createStub(ServerRequestInterface::class);
        $this->requestStub
            ->method('getAttribute')
            ->willReturnCallback(function (string $attribute): ?Site {
                if ($attribute === 'site') {
                    return $this->siteStub;
                }
                if ($attribute === 'nonce') {
                    return null;
                }
                throw new \InvalidArgumentException('Attribute "' . $attribute . '" not considered in stub callback');
            });
    }

    #[Test]
    #[DataProvider('dataProviderForAttributeTests')]
    public function enrichScriptTagViaEventAddsIdTypeAndDataAttributes(
        string $id,
        string $type,
        array $data,
        string $expected,
    ): void {
        $enrichEvent = new EnrichScriptTagEvent($this->requestStub);
        $enrichEvent->setId($id);
        $enrichEvent->setType($type);
        foreach ($data as $name => $value) {
            $enrichEvent->addDataAttribute($name, $value);
        }

        $eventDispatcherStub = self::createStub(EventDispatcherInterface::class);
        $eventDispatcherStub
            ->method('dispatch')
            ->willReturn($enrichEvent);

        $scriptTagBuilder = new ScriptTagBuilder(
            $eventDispatcherStub,
        );
        $scriptTagBuilder->setRequest($this->requestStub);

        self::assertSame(
            $expected,
            $scriptTagBuilder->build('/* some tracking code */'),
        );
    }

    public static function dataProviderForAttributeTests(): iterable
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

        yield 'Data attribute value provoking XSS' => [
            'id' => '',
            'type' => '',
            'data' => [
                'xss-try' => '"></script><svg/onload=prompt(document.domain)>',
            ],
            'expected' => '<script data-xss-try="&quot;&gt;&lt;/script&gt;&lt;svg/onload=prompt(document.domain)&gt;">/* some tracking code */</script>',
        ];
    }

    #[Test]
    public function nonceAttributeIsAddedCorrectly(): void
    {
        if ((new Typo3Version())->getMajorVersion() < 12) {
            self::markTestSkipped('Only for TYPO3 v12+');
        }

        $enrichEvent = new EnrichScriptTagEvent($this->requestStub);
        $eventDispatcherStub = self::createStub(EventDispatcherInterface::class);
        $eventDispatcherStub
            ->method('dispatch')
            ->willReturn($enrichEvent);

        $requestStub = self::createStub(ServerRequestInterface::class);
        $requestStub
            ->method('getAttribute')
            ->willReturnCallback(function (string $attribute): Stub|ConsumableString {
                if ($attribute === 'site') {
                    return $this->siteStub;
                }
                if ($attribute === 'nonce') {
                    return new ConsumableString('some-nonce');
                }
                throw new \InvalidArgumentException('Attribute "' . $attribute . '" not considered in stub callback');
            });

        $scriptTagBuilder = new ScriptTagBuilder(
            $eventDispatcherStub,
        );
        $scriptTagBuilder->setRequest($requestStub);

        self::assertSame(
            '<script nonce="some-nonce">/* some tracking code */</script>',
            $scriptTagBuilder->build('/* some tracking code */'),
        );
    }
}
