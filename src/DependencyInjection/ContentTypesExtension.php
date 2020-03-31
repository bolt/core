<?php
declare(strict_types=1);

namespace Bolt\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class ContentTypesExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
       
        
        $configuration = new ContentTypeConfiguration();
        $this->processConfiguration($configuration, $configs);
    }
}
