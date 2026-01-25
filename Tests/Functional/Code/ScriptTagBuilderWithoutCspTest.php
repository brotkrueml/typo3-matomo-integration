<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Functional\Code;

use Brotkrueml\MatomoIntegration\Code\ScriptTagBuilder;
use Brotkrueml\MatomoIntegration\Extension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(ScriptTagBuilder::class)]
final class ScriptTagBuilderWithoutCspTest extends FunctionalTestCase
{
    /**
     * @var list<string>
     */
    protected array $testExtensionsToLoad = [
        'brotkrueml/typo3-matomo-integration',
    ];

    /**
     * @var array<string, string>
     */
    protected array $pathsToProvideInTestInstance = [
        'typo3conf/ext/matomo_integration/Tests/Functional/Fixtures/Sites/' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCsvDataSet(__DIR__ . '/../Fixtures/pages.csv');
        $this->setUpFrontendRootPage(
            1,
            ['EXT:' . Extension::KEY . '/Tests/Functional/Fixtures/page.typoscript'],
        );
    }

    #[Test]
    public function nonceAttributeIsOmittedInScriptTagWithDeactivatedCsp(): void
    {
        $request = (new InternalRequest('http://localhost/'))
            ->withPageId(1);

        $response = $this->executeFrontendSubRequest($request);
        $response->getBody()->rewind();
        $body = $response->getBody()->getContents();

        self::assertStringContainsString(
            '<script>if(typeof _paq==="undefined"||!(_paq instanceof Array))var _paq=[];_paq.push(["disableCookies"]);_paq.push(["trackPageView"]);_paq.push(["enableLinkTracking"]);(function(){var u="//matomo.example.org/";_paq.push(["setTrackerUrl",u+"matomo.php"]);_paq.push(["setSiteId",1]);var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src=u+"matomo.js";s.parentNode.insertBefore(g,s);})();var _mtm=window._mtm||[];_mtm.push(["enableDebugMode"]);_mtm.push({"mtm.startTime":(new Date().getTime()),"event":"mtm.Start"});var d=document,g=d.createElement("script"),s=d.getElementsByTagName("script")[0];g.async=true;g.src="//matomo.example.org/js/container_EIBXLbSx.js";s.parentNode.insertBefore(g,s);</script>',
            $body,
        );
    }
}
