<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Repository\ContentRepository;
use Bolt\Twig\ContentExtension;
use Bolt\Version;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BackendMenuBuilder implements BackendMenuBuilderInterface
{
    public const MAX_LATEST_RECORDS = 5;

    /** @var FactoryInterface */
    private $menuFactory;

    /** @var Config */
    private $config;

    /** @var ContentRepository */
    private $contentRepository;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ContentExtension */
    private $contentExtension;

    /** @var ExtensionBackendMenuInterface[] */
    private $extensionMenus;

    public function __construct(
        FactoryInterface $menuFactory,
        iterable $extensionMenus,
        Config $config,
        ContentRepository $contentRepository,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        ContentExtension $contentExtension
    ) {
        $this->menuFactory = $menuFactory;
        $this->config = $config;
        $this->contentRepository = $contentRepository;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->contentExtension = $contentExtension;
        $this->extensionMenus = $extensionMenus;
    }

    private function createAdminMenu(): ItemInterface
    {
        $t = $this->translator;

        /** @var MenuItem $menu */
        $menu = $this->menuFactory->createItem('root');

        $menu->addChild('Dashboard', [
            'uri' => $this->urlGenerator->generate('bolt_dashboard'),
            'extras' => [
                'name' => $t->trans('caption.dashboard'),
                'icon' => 'fa-tachometer-alt',
            ],
        ]);

        $menu->addChild('Content', [
            'extras' => [
                'name' => $t->trans('caption.content'),
                'type' => 'separator',
                'icon' => 'fa-file',
            ],
        ]);

        $this->addContentItems($menu);

        $this->addContentOthers($menu);

        $this->addExtensionMenus($menu);

        $menu->addChild('Settings', [
            'extras' => [
                'name' => $t->trans('caption.settings'),
                'type' => 'separator',
                'icon' => 'fa-wrench',
            ],
        ]);

        // Configuration submenu

        $menu->addChild('Configuration', [
            'uri' => $this->urlGenerator->generate('bolt_menupage', [
                'slug' => 'configuration',
            ]),
            'extras' => [
                'name' => $t->trans('caption.configuration'),
                'icon' => 'fa-sliders-h',
                'slug' => 'configuration',
            ],
        ]);

        $menu->getChild('Configuration')->addChild('Users &amp; Permissions', [
            'uri' => $this->urlGenerator->generate('bolt_users'),
            'extras' => [
                'name' => $t->trans('caption.users_permissions'),
                'icon' => 'fa-users',
            ],
        ]);

        $menu->getChild('Configuration')->addChild('Main configuration', [
            'uri' => $this->urlGenerator->generate('bolt_file_edit', [
                'location' => 'config',
                'file' => '/bolt/config.yaml',
            ]),
            'extras' => [
                'name' => $t->trans('caption.main_configuration'),
                'icon' => 'fa-cog',
            ],
        ]);

        $menu->getChild('Configuration')->addChild('ContentTypes', [
            'uri' => $this->urlGenerator->generate('bolt_file_edit', [
                'location' => 'config',
                'file' => '/bolt/contenttypes.yaml',
            ]),
            'extras' => [
                'name' => $t->trans('caption.contenttypes'),
                'icon' => 'fa-object-group',
            ],
        ]);

        $menu->getChild('Configuration')->addChild('Taxonomies', [
            'uri' => $this->urlGenerator->generate('bolt_file_edit', [
                'location' => 'config',
                'file' => '/bolt/taxonomy.yaml',
            ]),
            'extras' => [
                'name' => $t->trans('caption.taxonomies'),
                'icon' => 'fa-tags',
            ],
        ]);

        $menu->getChild('Configuration')->addChild('Menu set up', [
            'uri' => $this->urlGenerator->generate('bolt_file_edit', [
                'location' => 'config',
                'file' => '/bolt/menu.yaml',
            ]),
            'extras' => [
                'name' => $t->trans('caption.menu_setup'),
                'type' => 'separator',
                'icon' => 'fa-list',
            ],
        ]);

        $menu->getChild('Configuration')->addChild('Routing set up', [
            'uri' => $this->urlGenerator->generate('bolt_file_edit', [
                'location' => 'config',
                'file' => '/routes.yaml',
            ]),
            'extras' => [
                'name' => $t->trans('caption.routing_setup'),
                'icon' => 'fa-directions',
            ],
        ]);

        $menu->getChild('Configuration')->addChild('All configuration files', [
            'uri' => $this->urlGenerator->generate('bolt_filemanager', ['location' => 'config']),
            'extras' => [
                'name' => $t->trans('caption.all_configuration_files'),
                'icon' => 'fa-cogs',
            ],
        ]);

        // Maintenance submenu

        $menu->addChild('Maintenance', [
            'uri' => $this->urlGenerator->generate('bolt_menupage', [
                'slug' => 'maintenance',
            ]),
            'extras' => [
                'name' => $t->trans('caption.maintenance'),
                'icon' => 'fa-tools',
                'slug' => 'maintenance',
            ],
        ]);

        $menu->getChild('Maintenance')->addChild('Extensions', [
            'uri' => $this->urlGenerator->generate('bolt_extensions'),
            'extras' => [
                'name' => $t->trans('caption.extensions'),
                'icon' => 'fa-plug',
            ],
        ]);

        $menu->getChild('Maintenance')->addChild('Log viewer', [
            'uri' => $this->urlGenerator->generate('bolt_logviewer'),
            'extras' => [
                'name' => $t->trans('caption.logviewer'),
                'icon' => 'fa-clipboard',
            ],
        ]);

        $menu->getChild('Maintenance')->addChild('Bolt API', [
            'uri' => $this->urlGenerator->generate('api_entrypoint'),
            'extras' => [
                'name' => $t->trans('caption.api'),
                'icon' => 'fa-code',
            ],
        ]);

        /*
         * @todo Make fixtures work from the backend
         */
        // $menu->getChild('Maintenance')->addChild('Fixtures', [
        //     'uri' => '',
        //     'extras' => [
        //         'name' => $t->trans('caption.fixtures_dummy_content'),
        //         'icon' => 'fa-hat-wizard',
        //     ],
        // ]);

        $menu->getChild('Maintenance')->addChild('Clear the cache', [
            'uri' => $this->urlGenerator->generate('bolt_clear_cache'),
            'extras' => [
                'name' => $t->trans('caption.clear_cache'),
                'icon' => 'fa-eraser',
            ],
        ]);

        /*
         * @todo Make Installation checks work from the backend
         */
        // $menu->getChild('Maintenance')->addChild('Installation checks', [
        //     'uri' => '',
        //     'extras' => [
        //         'name' => $t->trans('caption.installation_checks'),
        //         'icon' => 'fa-clipboard-check',
        //     ],
        // ]);

        $menu->getChild('Maintenance')->addChild('Translations', [
            'uri' => $this->urlGenerator->generate('translation_index'),
            'extras' => [
                'name' => $t->trans('caption.translations'),
                'icon' => 'fa-language',
            ],
        ]);

        // Hide this menu item, unless we're on a "Git clone" install.
        if (Version::installType() === 'Git clone') {
            $menu->getChild('Maintenance')->addChild('The Kitchensink', [
                'uri' => $this->urlGenerator->generate('bolt_kitchensink'),
                'extras' => [
                    'name' => $t->trans('caption.kitchensink'),
                    'icon' => 'fa-bath',
                ],
            ]);
        }

        $menu->getChild('Maintenance')->addChild('About Bolt', [
            'uri' => $this->urlGenerator->generate('bolt_about'),
            'extras' => [
                'name' => $t->trans('caption.about_bolt'),
                'icon' => 'fa-award',
            ],
        ]);

        // File Management submenu

        $menu->addChild('File Management', [
            'uri' => $this->urlGenerator->generate('bolt_menupage', [
                'slug' => 'filemanagement',
            ]),
            'extras' => [
                'name' => $t->trans('caption.file_management'),
                'icon' => 'fa-folder-open',
                'slug' => 'filemanagement',
            ],
        ]);

        $menu->getChild('File Management')->addChild('Uploaded files', [
            'uri' => $this->urlGenerator->generate('bolt_filemanager', ['location' => 'files']),
            'extras' => [
                'name' => $t->trans('caption.uploaded_files'),
                'icon' => 'fa-archive',
            ],
        ]);

        $menu->getChild('File Management')->addChild('View/edit Templates', [
            'uri' => $this->urlGenerator->generate('bolt_filemanager', ['location' => 'themes']),
            'extras' => [
                'name' => $t->trans('caption.view_edit_templates'),
                'icon' => 'fa-scroll',
            ],
        ]);

        return $menu;
    }

    private function addContentItems(MenuItem $menu): void
    {
        /** @var ContentType[] $contentTypes */
        $contentTypes = $this->config->get('contenttypes')->whereStrict('show_in_menu', true);

        foreach ($contentTypes as $contentType) {
            $menu->addChild($contentType->getSlug(), [
                'uri' => $this->urlGenerator->generate('bolt_content_overview', ['contentType' => $contentType->getSlug()]),
                'extras' => [
                    'name' => $contentType['name'],
                    'singular_name' => $contentType['singular_name'],
                    'slug' => $contentType->getSlug(),
                    'singular_slug' => $contentType['singular_slug'],
                    'icon' => $contentType['icon_many'],
                    'link_new' => $this->urlGenerator->generate('bolt_content_new', ['contentType' => $contentType->getSlug()]),
                    'link_listing' => $contentType->getSlug(),
                    'singleton' => $contentType['singleton'],
                    'active' => $contentType->getSlug() === 'pages' ? true : false,
                    'submenu' => $this->getLatestRecords($contentType),
                ],
            ]);
        }
    }

    private function addContentOthers(MenuItem $menu): void
    {
        $t = $this->translator;
        /** @var ContentType[] $contentTypes */
        $contentTypes = $this->config->get('contenttypes')->where('show_in_menu', '!==', true);

        foreach ($contentTypes as $contentType) {
            $label = $contentType->get('show_in_menu') ?: $t->trans('caption.other_content');

            if (! $menu->getChild($label)) {
                // Add the top level item
                $menu->addChild($label, [
                    'extras' => [
                        'name' => $label,
                        'icon' => $contentType->get('icon_many'),
                        'slug' => $label,
                    ],
                ]);
            }

            // Add the children to it.
            $menu->getChild($label)->addChild($contentType->get('slug'), [
                'uri' => $this->urlGenerator->generate('bolt_content_overview', ['contentType' => $contentType->getSlug()]),
                'extras' => [
                    'name' => $contentType->get('name'),
                    'icon' => $contentType->get('icon_many'),
                    'singleton' => $contentType->get('singleton'),
                ],
            ]);
        }
    }

    private function getLatestRecords(ContentType $contentType): array
    {
        $records = $this->contentRepository->findLatest($contentType, 1, self::MAX_LATEST_RECORDS);

        $result = [];

        foreach ($records as $record) {
            try {
                $additionalResult = [
                    'id' => $record->getId(),
                    'name' => $this->contentExtension->getTitle($record),
                    'link' => $this->contentExtension->getLink($record),
                    'editLink' => $this->contentExtension->getEditLink($record),
                    'icon' => $record->getIcon(),
                ];

                $result[] = $additionalResult;
            } catch (\RuntimeException $exception) {
                // When a record is not initialised (yet), don't break, but fail gracefully.
            }
        }

        return $result;
    }

    private function addExtensionMenus(MenuItem $menu): void
    {
        foreach ($this->extensionMenus as $extensionMenu) {
            $extensionMenu->addItems($menu);
        }
    }

    public function buildAdminMenu(): array
    {
        $menu = $this->createAdminMenu()->getChildren();

        $menuData = [];

        foreach ($menu as $child) {
            $submenu = [];

            if ($child->hasChildren()) {
                foreach ($child->getChildren() as $submenuChild) {
                    $submenu[] = [
                        'name' => $submenuChild->getExtra('name') ?: $submenuChild->getLabel(),
                        'icon' => $submenuChild->getExtra('icon'),
                        'editLink' => $submenuChild->getUri(),
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
                'singleton' => $child->getExtra('singleton'),
                'type' => $child->getExtra('type'),
                'active' => $child->getExtra('active'),
                'submenu' => $submenu,
            ];
        }

        return $menuData;
    }
}
