<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Domain\TrackingCode;

use Brotkrueml\MatomoIntegration\Domain\Dto\Configuration;
use Brotkrueml\MatomoIntegration\Domain\TrackingCode\NoScriptTrackingCodeBuilder;
use PHPUnit\Framework\TestCase;

final class NoScriptTrackingCodeBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function trackingCodeIsGeneratedCorrectly(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.com/',
            'matomoIntegrationSiteId' => 42,
        ]);

        $subject = new NoScriptTrackingCodeBuilder($configuration);

        self::assertSame(
            '<img src="https://www.example.com/matomo.php?idsite=42&amp;rec=1" style="border:0;" alt="">',
            $subject->getTrackingCode()
        );
    }
}
