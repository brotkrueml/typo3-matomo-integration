<?php

declare(strict_types=1);

/*
 * This file is part of the "matomo_integration" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\MatomoIntegration\Tests\Unit\Code;

use Brotkrueml\MatomoIntegration\Code\NoScriptTrackingCodeBuilder;
use Brotkrueml\MatomoIntegration\Entity\Configuration;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NoScriptTrackingCodeBuilderTest extends TestCase
{
    #[Test]
    public function trackingCodeIsGeneratedCorrectly(): void
    {
        $configuration = Configuration::createFromSiteConfiguration([
            'matomoIntegrationUrl' => 'https://www.example.com/',
            'matomoIntegrationSiteId' => 42,
        ]);

        $subject = (new NoScriptTrackingCodeBuilder())->setConfiguration($configuration);

        self::assertSame(
            '<img src="https://www.example.com/matomo.php?idsite=42&amp;rec=1" style="border:0;" alt="">',
            $subject->getTrackingCode(),
        );
    }
}
