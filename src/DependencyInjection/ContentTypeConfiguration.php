<?php
declare(strict_types=1);

namespace Bolt\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ContentTypeConfiguration implements ConfigurationInterface
{
    public function __construct()
    {
    }
    
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('content_types');
        $nodeRoot = $treeBuilder->getRootNode();
        $nodeRoot->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->children()
            ->scalarNode('name')->isRequired()->end()
            ->scalarNode('singular_name')->end()
            ->booleanNode('viewless')->defaultFalse()->end()
            ->arrayNode('taxonomy')
            ->beforeNormalization()->castToArray()->end()
            ->prototype('scalar')->end()
            ->end()
            ->arrayNode('locales')
            ->beforeNormalization()->castToArray()->end()
            ->prototype('scalar')->end()
            ->end()
            ->arrayNode('title_format')
            ->beforeNormalization()->castToArray()->end()
            ->prototype('scalar')->end()
            ->end()
            ->booleanNode('singleton')->defaultFalse()->end()
            ->enumNode('default_status')->values(['published'])->end()
            ->scalarNode('icon_many')->defaultNull()->end()
            ->scalarNode('icon_one')->defaultNull()->end()
            ->scalarNode('record_template')->defaultNull()->end()
            ->scalarNode('listing_template')->defaultNull()->end()
            ->scalarNode('sort')->defaultValue('-publishedAt')->end()
            ->integerNode('listing_records')->defaultValue(6)->end()
            ->integerNode('records_per_page')->defaultValue(10)->end()
            ->arrayNode('relations')
                     ->prototype('array')
                        ->children()
                            ->booleanNode('multiple')->defaultFalse()->end()
                            ->scalarNode('order')->defaultValue('-id')->end()
                            ->scalarNode('label')->defaultNull()->end()
                            ->scalarNode('formal')->defaultNull()->end()
                            ->scalarNode('postfix')->defaultNull()->end()
                    
                        ->end()
            ->end()
            ->end()
            

            ->append($this->buildFields())
            ->end()
            ->end()
        ;
        
        return $treeBuilder;
    }
    
    private function buildFields()
    {
        $treeBuilder = new TreeBuilder('fields');
        $node = $treeBuilder->getRootNode()
            ->isRequired()
            ->arrayPrototype()
            ->children()
    
            ->enumNode('type')->values(['text', 'select', 'html', 'image'])
            ->end()
            ->scalarNode('class')->defaultNull()->end()
            ->scalarNode('group')->defaultNull()->end()
            ->scalarNode('label')->isRequired()->defaultNull()->end()
            ->scalarNode('postfix')->defaultNull()->end()
            ->scalarNode('filter')->defaultNull()->end()
            ->scalarNode('height')->defaultValue('150px')->end()
            ->scalarNode('placeholder')->defaultNull()->end()
            ->booleanNode('localize')->defaultFalse()->end()
            ->booleanNode('allow_twig')->defaultFalse()->end()
            ->booleanNode('required')->defaultFalse()->end()
            ->scalarNode('pattern')->defaultNull()->end()
            ->scalarNode('error')->defaultNull()->end()
            ->scalarNode('prefix')->defaultNull()->end()
            ->scalarNode('info')->defaultNull()->end()
            ->booleanNode('separator')->defaultNull()->end()
            ->scalarNode('upload')->defaultNull()->end()
            ->scalarNode('alt')->defaultFalse()->end()
            ->integerNode('limit')->defaultValue(5)->end()
            ->scalarNode('mode')->defaultNull()->end()
            ->scalarNode('step')->defaultNull()->end()
            ->booleanNode('multiple')->defaultFalse()->end()
            ->booleanNode('autocomplete')->defaultFalse()->end()
            ->scalarNode('sort')->defaultNull()->end()
            
            ->arrayNode('uses')
                ->beforeNormalization()->castToArray()->end()
                ->prototype('scalar')->end()
    
            ->end()
            ->arrayNode('values')
                ->beforeNormalization()->castToArray()->end()
                ->prototype('scalar')->end()
    
            ->end()
            ->arrayNode('extensions')
                ->beforeNormalization()->castToArray()->end()
                ->prototype('scalar')->end()
    
            ->end()
    
            ->end()
            ->end()
        ;
        return $node;
    }
}
