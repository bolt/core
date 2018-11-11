<?php

declare(strict_types=1);

namespace Bolt\Content;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Knp\Menu\FactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilder
{
    /** @var FactoryInterface */
    private $factory;

    /** @var Config */
    private $config;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var ContentRepository */
    private $content;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * MenuBuilder constructor.
     *
     * @param FactoryInterface      $factory
     * @param Config                $config
     * @param Stopwatch             $stopwatch
     * @param ContentRepository     $content
     * @param UrlGeneratorInterface $urlGenerator
     * @param TranslatorInterface   $translator
     */
    public function __construct(FactoryInterface $factory, Config $config, Stopwatch $stopwatch, ContentRepository $content, UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator)
    {
        $this->config = $config;
        $this->factory = $factory;
        $this->stopwatch = $stopwatch;
        $this->content = $content;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    public function createSidebarMenu()
    {
        $this->stopwatch->start('bolt.sidebar');

        $t = $this->translator;

        $menu = $this->factory->createItem('root');

        $menu->addChild('Dashboard', [
            'uri' => $this->urlGenerator->generate('bolt_dashboard'),
            'extras' => [
                'name' => $t->trans('caption.dashboard'),
                'icon' => 'fa-tachometer-alt',
            ],
        ]);

        $menu->addChild('Content', ['extras' => [
            'name' => $t->trans('caption.content'),
            'type' => 'separator',
            'icon' => 'fa-file',
        ]]);

        $contenttypes = $this->config->get('contenttypes');

        foreach ($contenttypes as $contenttype) {
            $menu->addChild($contenttype['slug'], [
                'uri' => $this->urlGenerator->generate('bolt_contentlisting', ['contenttype' => $contenttype['slug']]),
                'extras' => [
                    'name' => $contenttype['name'],
                    'singular_name' => $contenttype['singular_name'],
                    'slug' => $contenttype['slug'],
                    'singular_slug' => $contenttype['singular_slug'],
                    'icon' => $contenttype['icon_many'],
                    'link_new' => $this->urlGenerator->generate('bolt_edit_record', ['id' => $contenttype['slug']]),
                    'contenttype' => $contenttype['slug'],
                    'singleton' => $contenttype['singleton'],
                    'active' => $contenttype['slug'] === 'pages' ? true : false,
                    'submenu' => $this->getLatestRecords($contenttype['slug']),
                ], ]);
        }

        $menu->addChild('Settings', ['extras' => [
            'name' => $t->trans('caption.settings'),
            'type' => 'separator',
            'icon' => 'fa-wrench',
        ]]);

        // Configuration submenu

        $menu->addChild('Configuration', ['extras' => [
            'name' => $t->trans('caption.configuration'),
            'icon' => 'fa-sliders-h',
        ]]);

        $menu['Configuration']->addChild('Users &amp; Permissions', [
            'uri' => $this->urlGenerator->generate('bolt_users'),
            'extras' => [
                'name' => $t->trans('caption.users_permissions'),
                'icon' => 'fa-users',
            ],
        ]);

        $menu['Configuration']->addChild('Main configuration', [
            'uri' => $this->urlGenerator->generate('bolt_edit_file', ['area' => 'config', 'file' => '/bolt/config.yaml']),
            'extras' => [
                'name' => $t->trans('caption.main_configuration'),
                'icon' => 'fa-cog',
            ],
        ]);

        $menu['Configuration']->addChild('ContentTypes', [
            'uri' => $this->urlGenerator->generate('bolt_edit_file', ['area' => 'config', 'file' => '/bolt/contenttypes.yaml']),
            'extras' => [
                'name' => $t->trans('caption.contenttypes'),
                'icon' => 'fa-object-group',
            ],
        ]);

        $menu['Configuration']->addChild('Taxonomies', [
            'uri' => $this->urlGenerator->generate('bolt_edit_file', ['area' => 'config', 'file' => '/bolt/taxonomy.yaml']),
            'extras' => [
                'name' => $t->trans('caption.taxonomies'),
                'icon' => 'fa-tags',
            ],
        ]);

        $menu['Configuration']->addChild('Menu set up', [
            'uri' => $this->urlGenerator->generate('bolt_edit_file', ['area' => 'config', 'file' => '/bolt/menu.yaml']),
            'extras' => [
                'name' => $t->trans('caption.menu_setup'),
                'type' => 'separator',
                'icon' => 'fa-list',
            ],
        ]);

        $menu['Configuration']->addChild('Routing set up', [
            'uri' => $this->urlGenerator->generate('bolt_edit_file', ['area' => 'config', 'file' => '/routes.yaml']),
            'extras' => [
                'name' => $t->trans('caption.routing_setup'),
                'icon' => 'fa-directions',
            ],
        ]);

        $menu['Configuration']->addChild('All configuration files', [
            'uri' => $this->urlGenerator->generate('bolt_finder', ['area' => 'config']),
            'extras' => [
                'name' => $t->trans('caption.all_configuration_files'),
                'icon' => 'fa-cogs',
            ],
        ]);

        // Maintenance submenu

        $menu->addChild('Maintenance', ['extras' => [
            'name' => $t->trans('caption.maintenance'),
            'icon' => 'fa-wrench',
        ]]);

        $menu['Maintenance']->addChild('Bolt API', [
            'uri' => $this->urlGenerator->generate('api_entrypoint'),
            'extras' => [
                'name' => $t->trans('caption.api'),
                'icon' => 'fa-code',
            ],
        ]);

        $menu['Maintenance']->addChild('Check database', [
            'uri' => '',
            'extras' => [
                'name' => $t->trans('caption.check_database'),
                'icon' => 'fa-database',
            ],
        ]);

        $menu['Maintenance']->addChild('Fixtures', [
            'uri' => '',
            'extras' => [
                'name' => $t->trans('caption.fixtures_dummy_content'),
                'icon' => 'fa-hat-wizard',
            ],
        ]);

        $menu['Maintenance']->addChild('Clear the cache', [
            'uri' => '',
            'extras' => [
                'name' => $t->trans('caption.clear_cache'),
                'icon' => 'fa-eraser',
            ],
        ]);

        $menu['Maintenance']->addChild('Installation checks', [
            'uri' => '',
            'extras' => [
                'name' => $t->trans('caption.installation_checks'),
                'icon' => 'fa-clipboard-check',
            ],
        ]);

        $menu['Maintenance']->addChild('Translations: Messages', [
            'uri' => $this->urlGenerator->generate('translation_index'),
            'extras' => [
                'name' => $t->trans('caption.translations'),
                'icon' => 'fa-language',
            ],
        ]);

        $menu['Maintenance']->addChild('Extensions', [
            'uri' => '',
            'extras' => [
                'name' => $t->trans('caption.extensions'),
                'icon' => 'fa-plug',
            ],
        ]);

        // File Management submenu

        $menu->addChild('File Management', ['extras' => [
            'name' => $t->trans('caption.file_management'),
            'icon' => 'fa-folder-open',
        ]]);

        $menu['File Management']->addChild('Uploaded files', [
            'uri' => $this->urlGenerator->generate('bolt_finder', ['area' => 'files']),
            'extras' => [
                'name' => $t->trans('caption.uploaded_files'),
                'icon' => 'fa-archive',
            ],
        ]);

        $menu['File Management']->addChild('View/edit Templates', [
            'uri' => $this->urlGenerator->generate('bolt_finder', ['area' => 'themes']),
            'extras' => [
                'name' => $t->trans('caption.view_edit_templates'),
                'icon' => 'fa-scroll',
            ],
        ]);

        $this->stopwatch->stop('bolt.sidebar');

        return $menu;
    }

    private function getLatestRecords($slug)
    {
        /** @var ContentType $ct */
        $contenttype = ContentType::factory($slug, $this->config->get('contenttypes'));

        /** @var Content $records */
        $records = $this->content->findLatest($contenttype, 5);

        $result = [];

        /** @var Content $record */
        foreach ($records as $record) {
            $result[] = [
                'id' => $record->getId(),
                'name' => $record->magicTitle(),
                'link' => $record->magicLink(),
                'editlink' => $record->magicEditLink(),
                'icon' => $record->getDefinition()->get('icon_one'),
            ];
        }

        return $result;
    }

    public function getMenu()
    {
        $menu = $this->createSidebarMenu()->getChildren();

        $menuData = [];

        foreach ($menu as $child) {
            $submenu = [];

            if ($child->hasChildren()) {
                foreach ($child->getChildren() as $submenuChild) {
                    $submenu[] = [
                        'name' => $submenuChild->getExtra('name') ?: $submenuChild->getLabel(),
                        'singular_name' => $submenuChild->getExtra('singular_name'),
                        'slug' => $submenuChild->getExtra('slug'),
                        'singular_slug' => $submenuChild->getExtra('singular_slug'),
                        'icon' => $submenuChild->getExtra('icon'),
                        'link' => $submenuChild->getUri(),
                        'link_new' => $submenuChild->getExtra('link_new'),
                        'contenttype' => $submenuChild->getExtra('contenttype'),
                        'singleton' => $submenuChild->getExtra('singleton'),
                        'type' => $submenuChild->getExtra('type'),
                        'active' => $submenuChild->getExtra('active'),
                    ];
                }
            } else {
                $submenu = $child->getExtra('submenu');
            }

            $menuData[] = [
                'name' => $child->getExtra('name') ?: $child->getLabel(),
                'singular_name' => $child->getExtra('singular_name'),
                'slug' => $child->getExtra('slug'),
                'singular_slug' => $child->getExtra('singular_slug'),
                'icon' => $child->getExtra('icon'),
                'link' => $child->getUri(),
                'link_new' => $child->getExtra('link_new'),
                'contenttype' => $child->getExtra('contenttype'),
                'singleton' => $child->getExtra('singleton'),
                'type' => $child->getExtra('type'),
                'active' => $child->getExtra('active'),
                'submenu' => $submenu,
            ];
        }

        return $menuData;
    }
}
