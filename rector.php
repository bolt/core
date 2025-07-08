<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withAutoloadPaths([__DIR__.'/vendor/autoload.php'])
    ->withImportNames(importShortClasses: false, removeUnusedImports: true)
    ->withPhpSets(php81: true)
    ->withTypeCoverageLevel(0)
    //->withSets([
        //\Rector\Symfony\Set\SymfonySetList::SYMFONY_64,
        //\Rector\Symfony\Set\SensiolabsSetList::ANNOTATIONS_TO_ATTRIBUTES,
        //\Rector\Doctrine\Set\DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
    //])
    ;
