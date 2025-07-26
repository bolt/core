<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withCache('./var/cache/rector', FileCacheStorage::class)
    ->withPaths(['./src'])
    ->withImportNames()
    ->withParallel(timeoutSeconds: 180, jobSize: 10)
    ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/App_KernelDevDebugContainer.xml')
    ->withSymfonyContainerPhp(__DIR__ . '/tests/rector/symfony-container.php')
    ->withPhpSets()
    ->withSkip([
        Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class => [
            'src/Entity/Relation.php',
            'src/Entity/ResetPasswordRequest.php',
        ]
    ]);
