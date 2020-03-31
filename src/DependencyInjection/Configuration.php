<?php
declare(strict_types=1);

namespace Bolt\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    private const DEFAULT_ALLOWED_TAGS = [
        'div',
        'span',
        'p',
        'br',
        'hr',
        's',
        'u',
        'strong',
        'em',
        'i',
        'b',
        'li',
        'ul',
        'ol',
        'mark',
        'blockquote',
        'pre',
        'code',
        'tt',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'dd',
        'dl',
        'dt',
        'table',
        'tbody',
        'thead',
        'tfoot',
        'th',
        'td',
        'tr',
        'a',
        'img',
        'address',
        'abbr',
        'iframe',
        'caption',
        'sub',
        'super',
        'figure',
        'figcaption',
    ];
    private const DEFAULT_ALLOWED_ATTRIBUTES = [
        'id',
        'class',
        'style',
        'name',
        'value',
        'href',
        'src',
        'alt',
        'title',
        'width',
        'height',
        'frameborder',
        'allowfullscreen',
        'scrolling',
        'target',
        'colspan',
        'rowspan',
    ];
    private const DEFAULT_ACCEPT_FILE_TYPES = [
        'twig',
        'html',
        'js',
        'css',
        'scss',
        'gif',
        'jpg',
        'jpeg',
        'png',
        'ico',
        'zip',
        'tgz',
        'txt',
        'md',
        'doc',
        'docx',
        'pdf',
        'epub',
        'xls',
        'xlsx',
        'ppt',
        'pptx',
        'mp3',
        'ogg',
        'wav',
        'm4a',
        'mp4',
        'm4v',
        'ogv',
        'wmv',
        'avi',
        'webm',
        'svg',
    ];
    private const DEFAULT_MEDIA_TYPES = ['gif', 'jpg', 'jpeg', 'png', 'svg', 'pdf', 'mp3', 'tiff'];
    
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('bolt');
        $nodeRoot = $treeBuilder->getRootNode();
        $nodeRoot->children()
            ->scalarNode('sitename')->defaultValue('Default Bolt site')
            ->end()
            ->scalarNode('locale')->defaultNull()->end()
            ->scalarNode('secret')->end()
            ->scalarNode('payoff')->defaultNull()->end()
            ->scalarNode('homepage')->defaultValue('homepage/1')->end()
            ->scalarNode('homepage_template')->defaultValue('index.twig')->end()
            ->arrayNode('notfound')
            ->beforeNormalization()->castToArray()->end()
            ->defaultValue(['blocks/404-not-found', 'helpers/page_404.html.twig'])
            ->prototype('scalar')->end()
            ->end()
            ->integerNode('records_per_page')->defaultValue(10)->end()
            ->integerNode('records_on_dashboard')->defaultValue(5)->end()
            ->scalarNode('record_template')->defaultValue('record.twig')->end()
            ->scalarNode('debug')->defaultNull()->end()
            ->booleanNode('debug_show_loggedoff')->defaultFalse()->end()
            ->scalarNode('debug_error_level')->defaultNull()->end()
            ->scalarNode('production_error_level')->defaultNull()->end()
            ->scalarNode('strict_variables')->defaultNull()->end()
            ->scalarNode('theme')->defaultValue('base-2019')->end()
            ->scalarNode('listing_template')->defaultValue('listing.html.twig')->end()
            ->integerNode('listing_records')->defaultValue(5)->end()
            ->scalarNode('listing_sort')->defaultValue('datepublish DESC')->end()
            ->enumNode('taxonomy_sort')->values(['DESC', 'ASC'])->end()
            ->scalarNode('search_results_template')->defaultValue('search.twig')->end()
            ->integerNode('search_results_records')->defaultValue(10)->end()
            ->integerNode('maximum_listing_select')->defaultValue(1000)->end()
            ->scalarNode('cron_hour')->defaultValue(3)->end()
            ->booleanNode('omit_backgrounds')->defaultTrue()->end()
            ->booleanNode('maintenance_mode')->defaultFalse()->end()
            ->booleanNode('enforce_ssl')->defaultFalse()->end()
          
            ->arrayNode('maintenance')
            ->beforeNormalization()->castToArray()->end()
            ->defaultValue([])
            ->prototype('scalar')->end()
            ->end()
            ->arrayNode('accept_file_types')
            ->beforeNormalization()->castToArray()->end()
            ->defaultValue(self::DEFAULT_ACCEPT_FILE_TYPES)
            ->prototype('scalar')->end()
            ->end()
            ->arrayNode('accept_media_types')
                ->beforeNormalization()->castToArray()->end()
                ->defaultValue(self::DEFAULT_MEDIA_TYPES)
                ->prototype('scalar')->end()
            ->end()
            ->end()
        ;
        $this->buildPerformance($nodeRoot);
        $this->buildThumbnails($nodeRoot);
        $this->buildDatabase($nodeRoot);
        $this->buildWysiwyg($nodeRoot);
        $this->buildHtmlCleaner($nodeRoot);
        $this->buildBranding($nodeRoot);
        $this->buildHeaders($nodeRoot);
        return $treeBuilder;
    }
    
    private function buildPerformance(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
            ->arrayNode('performance')
                ->children()
                    ->arrayNode('http_cache')
                        ->beforeNormalization()->castToArray()->end()
                        ->children()
                            ->arrayNode('options')
                                ->beforeNormalization()->castToArray()->end()
                        ->end()
                        ->end()
                        ->end()
    
                    ->arrayNode('timed_records')
                        ->beforeNormalization()->castToArray()->end()
                        ->children()
                            ->integerNode('interval')->defaultValue(3600)->end()
                            ->booleanNode('use_cron')->defaultFalse()->end()
            ->end()
            ;
    
    }
    
    private function buildHeaders(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
            ->arrayNode('headers')
                ->children()
                    ->booleanNode('x_frame_options')->defaultTrue()->end()
            ->end();
    }
    
    private function buildBranding(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
            ->scalarNode('name')->defaultValue('Bolt')->end()
            ->scalarNode('path')->defaultValue('/bolt')->end()
            ->arrayNode('provided_by')->end()
            ->end();
    }
    private function buildThumbnails(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
            ->arrayNode('thumbnails')
                    ->beforeNormalization()->castToArray()->end()
                ->children()
                    ->arrayNode('default_thumbnail')
                        ->prototype('integer')
                    ->end()
                    ->end()
                    ->arrayNode('default_image')
                        ->prototype('integer')
                    ->end()
                    ->end()
                    ->integerNode('quality')->defaultValue(75)->end()
                    ->scalarNode('cropping')->defaultValue('crop')->end()
                    ->scalarNode('notfound_image')->defaultValue('bolt_assets://img/default_notfound.png')->end()
                    ->scalarNode('error_image')->defaultValue( 'bolt_assets://img/default_error.png')->end()
                    ->booleanNode('only_aliases')->defaultFalse()->end()
            ->end();
    }
    private function buildDatabase(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
         ->arrayNode('database')
            ->children()
                ->scalarNode('driver')->defaultValue('sqlite')->end()
                ->scalarNode('host')->defaultValue('localhost')->end()
                ->scalarNode('dbname')->defaultValue('bolt')->end()
                ->scalarNode('prefix')->defaultValue('bolt_')->end()
                ->scalarNode('charset')->defaultValue('utf8')->end()
                ->scalarNode('collate')->defaultValue('utf8_unicode_ci')->end()
                ->scalarNode('randomfunction')->defaultValue('')->end()
            ->end()
            ->end() ;
            
    }
    
    private function buildCaching(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
                ->arrayNode('caching')
                    ->children()
                        ->booleanNode('config')->defaultTrue()->end()
                        ->booleanNode('templates')->defaultTrue()->end()
                        ->booleanNode('request')->defaultTrue()->end()
                        ->integerNode('duration')->defaultValue(10)->end()
            
                ->end();
    }
    
    private function buildWysiwyg(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
            ->arrayNode('wysiwyg')
            ->children()
                ->booleanNode('images')->defaultFalse()->end()
                ->booleanNode('anchor')->defaultFalse()->end()
                ->booleanNode('tables')->defaultFalse()->end()
                ->booleanNode('fontcolor')->defaultFalse()->end()
                ->booleanNode('align')->defaultFalse()->end()
                ->booleanNode('subsuper')->defaultFalse()->end()
                ->booleanNode('embed')->defaultFalse()->end()
                ->booleanNode('underline')->defaultFalse()->end()
                ->booleanNode('ruler')->defaultFalse()->end()
                ->booleanNode('strike')->defaultFalse()->end()
                ->booleanNode('blockquote')->defaultFalse()->end()
                ->booleanNode('codesnippet')->defaultFalse()->end()
                ->booleanNode('specialchar')->defaultFalse()->end()
                ->booleanNode('clipboard')->defaultFalse()->end()
                ->booleanNode('copypaste')->defaultFalse()->end()
                ->booleanNode('styles')->defaultFalse()->end()
                ->arrayNode('ck')
                    ->children()
                    ->arrayNode('contentsCss')
                        ->beforeNormalization()->castToArray()->end()
                        ->defaultValue([
                            ['css/ckeditor-contents.css', 'bolt'],
                            ['css/ckeditor.css', 'bolt']
                        ])
                        ->prototype('array')->scalarPrototype()->end()
                    ->end()
                ->end()
                
                    ->integerNode('filebrowserWindowWidth')->defaultValue(640)->end()
                    ->integerNode('filebrowserWindowHeight')->defaultValue(480)->end()
                    ->booleanNode('autoParagraph')->defaultTrue()->end()
                    ->booleanNode('disableNativeSpellChecker')->defaultTrue()->end()
                    ->booleanNode('allowNbsp')->defaultTrue()->end()
                ->end()
            ->end()
        ;
    }
    
    private function buildHtmlCleaner(ArrayNodeDefinition $rootNode)
    {
        $rootNode->children()
            ->arrayNode('htmlcleaner')
            ->children()
                ->arrayNode('allowed_tags')
                ->beforeNormalization()->castToArray()->end()
                ->defaultValue(self::DEFAULT_ALLOWED_TAGS)
                ->prototype('scalar')->end()
            ->end()
                ->arrayNode('allowed_attributes')
                ->beforeNormalization()->castToArray()->end()
                ->defaultValue(self::DEFAULT_ALLOWED_ATTRIBUTES)
                ->prototype('scalar')->end()
            ->end()
        ;
    }
}
