<?php
declare(strict_types=1);

namespace Bolt\DependenciesInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class BoltExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->processConfiguration(new Configuration(), $configs);
    }
}
