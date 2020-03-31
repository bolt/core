<?php
declare(strict_types=1);

namespace Bolt\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class BoltExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        
        $configuration = $this->getConfiguration($configs, $container);
        $this->processConfiguration($configuration, $configs);
        
    }
}
