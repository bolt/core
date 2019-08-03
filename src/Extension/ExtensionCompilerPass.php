<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtensionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has(ExtensionRegistry::class) === false) {
            return;
        }

        $repository = $container->findDefinition(ExtensionRegistry::class);
        // The important bit: grab all classes that were tagged with our specified CONTAINER_TAG, and shove them into our Repository
        foreach (array_keys($container->findTaggedServiceIds(ExtensionInterface::CONTAINER_TAG)) as $id) {
            /* @see ExtensionRegistry::addCompilerPass() */
            $repository->addMethodCall('addCompilerPass', [$id]);
        }
    }
}
