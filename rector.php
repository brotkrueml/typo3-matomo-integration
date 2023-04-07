<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeFromPropertyTypeRector;
use Rector\TypeDeclaration\Rector\FunctionLike\AddReturnTypeDeclarationFromYieldsRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;

return static function (RectorConfig $config): void {
    $config->phpVersion(PhpVersion::PHP_74);

    $config->sets([
        LevelSetList::UP_TO_PHP_74,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        PHPUnitSetList::PHPUNIT_CODE_QUALITY,
        PHPUnitSetList::PHPUNIT_EXCEPTION,
        PHPUnitSetList::PHPUNIT_SPECIFIC_METHOD,
        PHPUnitSetList::PHPUNIT_YIELD_DATA_PROVIDER,
    ]);

    $config->autoloadPaths([
        __DIR__ . '/.Build/vendor/autoload.php',
    ]);
    $config->paths([
        __DIR__ . '/Classes',
        __DIR__ . '/Tests',
    ]);
    $config->skip([
        AddLiteralSeparatorToNumberRector::class,
        AddParamTypeFromPropertyTypeRector::class => [
            __DIR__ . '/Classes/Code/MatomoMethodCall.php',
        ],
        AddReturnTypeDeclarationFromYieldsRector::class => [
            __DIR__ . '/Tests/*',
        ],
        RemoveUnusedPromotedPropertyRector::class, // to avoid rector warning on PHP8.0 with codebase compatible with PHP7.4
        TypedPropertyFromAssignsRector::class => [
            __DIR__ . '/Classes/Event/TrackSiteSearchEvent.php',
        ],
    ]);
};
