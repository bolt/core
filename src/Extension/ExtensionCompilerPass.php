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

        $registry = $container->findDefinition(ExtensionRegistry::class);
        $packages = array_keys($container->findTaggedServiceIds(ExtensionInterface::CONTAINER_TAG));

        /* @see ExtensionRegistry::addCompilerPass() */
        $registry->addMethodCall('addCompilerPass', [$packages]);

        // Remove our own `services_bolt.yml` file, so that it can be recreated
        $projectDir = $container->getParameter('kernel.project_dir');
        @unlink($projectDir . '/config/services_bolt.yaml');
    }
}
