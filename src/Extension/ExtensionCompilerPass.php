<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ExtensionCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ExtensionRegistry::class)) {
            return;
        }

        $repository = $container->findDefinition(ExtensionRegistry::class);
        // The important bit: grab all classes that were tagged with our specified CONTAINER_TAG, and shove them into our Repository
        foreach ($container->findTaggedServiceIds(ExtensionInterface::CONTAINER_TAG) as $id => $tags) {
            /** @see ExtensionRegistry::add() */
            $repository->addMethodCall('add', [new Reference($id)]);
        }
    }
}