<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Symfony\Bridge\Symfony\Routing\SymfonyRoutesProvider;
use Rector\Symfony\Contract\Bridge\Symfony\Routing\SymfonyRoutesProviderInterface;

return RectorConfig::configure()
    ->withCache('./var/cache/rector', FileCacheStorage::class)
    ->withPaths(['./src'])
    ->withImportNames()
    ->withParallel(timeoutSeconds: 180, jobSize: 10)
    ->withSymfonyContainerXml(__DIR__ . '/var/cache/dev/Bolt_KernelDevDebugContainer.xml')
    ->withSymfonyContainerPhp(__DIR__ . '/tests/symfony-container.php')
    ->registerService(SymfonyRoutesProvider::class, SymfonyRoutesProviderInterface::class)
    ->withPhpSets()
    ->withPreparedSets(
        typeDeclarations: true,
        symfonyCodeQuality: true,
    )
    ->withComposerBased(
        twig: true,
        doctrine: true,
        phpunit: true,
        symfony: true,

    )
    ->withSets([
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,

    ])
    ->withRules([
        \Rector\Symfony\Configs\Rector\ClassMethod\AddRouteAnnotationRector::class,
            \Rector\Symfony\Symfony60\Rector\FuncCall\ReplaceServiceArgumentRector::class,
            \Rector\Symfony\Symfony60\Rector\MethodCall\GetHelperControllerToServiceRector::class,
            \Rector\Symfony\Symfony61\Rector\Class_\CommandConfigureToAttributeRector::class,
            \Rector\Symfony\Symfony61\Rector\Class_\CommandPropertyToAttributeRector::class,
            \Rector\Symfony\Symfony61\Rector\Class_\MagicClosureTwigExtensionToNativeMethodsRector::class,
            \Rector\Symfony\Symfony61\Rector\StaticPropertyFetch\ErrorNamesPropertyToConstantRector::class,
            \Rector\Symfony\Symfony62\Rector\Class_\MessageHandlerInterfaceToAttributeRector::class,
            \Rector\Symfony\Symfony62\Rector\Class_\MessageSubscriberInterfaceToAttributeRector::class,
            \Rector\Symfony\Symfony62\Rector\Class_\SecurityAttributeToIsGrantedAttributeRector::class,
            \Rector\Symfony\Symfony62\Rector\ClassMethod\ClassMethod\ArgumentValueResolverToValueResolverRector::class,
            \Rector\Symfony\Symfony62\Rector\ClassMethod\ParamConverterAttributeToMapEntityAttributeRector::class,
            \Rector\Symfony\Symfony62\Rector\MethodCall\SimplifyFormRenderingRector::class,
            \Rector\Symfony\Symfony63\Rector\Class_\ParamAndEnvAttributeRector::class,
            \Rector\Symfony\Symfony63\Rector\Class_\SignalableCommandInterfaceReturnTypeRector::class
        ]
    )
    ->withSkip([
        Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class => [
            'src/Entity/Relation.php',
            'src/Entity/ResetPasswordRequest.php',
        ]
    ]);
