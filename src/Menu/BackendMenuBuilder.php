<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Security\ContentVoter;
use Bolt\Twig\ContentExtension;
use Bolt\Utils\ListFormatHelper;
use Bolt\Version;
use Cocur\Slugify\Slugify;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuItem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tightenco\Collect\Support\Collection;

/**
 * Class BackendMenuBuilder
 */
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

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var ListFormatHelper */
    private $listFormatHelper;

    public function __construct(
        FactoryInterface $menuFactory,
        iterable $extensionMenus,
        Config $config,
        ContentRepository $contentRepository,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator,
        ContentExtension $contentExtension,
        AuthorizationCheckerInterface $authorizationChecker,
        ListFormatHelper $listFormatHelper
    ) {
        $this->menuFactory = $menuFactory;
        $this->config = $config;
        $this->contentRepository = $contentRepository;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->contentExtension = $contentExtension;
        $this->extensionMenus = $extensionMenus;
        $this->authorizationChecker = $authorizationChecker;
        $this->listFormatHelper = $listFormatHelper;
    }

    private function createAdminMenu(): ItemInterface
    {
        $t = $this->translator;

        /** @var MenuItem $menu */
        $menu = $this->menuFactory->createItem('root');

        if ($this->authorizationChecker->isGranted('dashboard')) {
            $menu->addChild('Dashboard', [
                'uri' => $this->urlGenerator->generate('bolt_dashboard'),
                'extras' => [
                    'name' => $t->trans('caption.dashboard'),
                    'icon' => 'fa-tachometer-alt',
                ],
            ]);
        }

        $menu->addChild('Content', [
            'extras' => [
                'name' => $t->trans('caption.content'),
                'type' => 'separator',
                'icon' => 'fa-file',
            ],
        ]);

        $this->addContentItems($menu);

        $this->addContentOthers($menu);

        if ($this->authorizationChecker->isGranted('extensionmenus')) {
            $this->addExtensionMenus($menu);
        }

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

        if ($this->authorizationChecker->isGranted('user:list')) {
            $menu->getChild('Configuration')->addChild('Users &amp; Permissions', [
                'uri' => $this->urlGenerator->generate('bolt_users'),
                'extras' => [
                    'name' => $t->trans('caption.users_permissions'),
                    'icon' => 'fa-users',
                ],
            ]);
        }

        if ($this->authorizationChecker->isGranted('managefiles:config')) {
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
        }

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

        if ($this->authorizationChecker->isGranted('extensions')) {
            $menu->getChild('Maintenance')->addChild('Extensions', [
                'uri' => $this->urlGenerator->generate('bolt_extensions'),
                'extras' => [
                    'name' => $t->trans('caption.extensions'),
                    'icon' => 'fa-plug',
                ],
            ]);
        }

        if ($this->authorizationChecker->isGranted('systemlog')) {
            $menu->getChild('Maintenance')->addChild('Log viewer', [
                'uri' => $this->urlGenerator->generate('bolt_logviewer'),
                'extras' => [
                    'name' => $t->trans('caption.logviewer'),
                    'icon' => 'fa-clipboard',
                ],
            ]);
        }

        if ($this->authorizationChecker->isGranted('api_admin')) {
            $menu->getChild('Maintenance')->addChild('Bolt API', [
                'uri' => $this->urlGenerator->generate('api_entrypoint'),
                'extras' => [
                    'name' => $t->trans('caption.api'),
                    'icon' => 'fa-code',
                ],
            ]);
        }

        if ($this->authorizationChecker->isGranted('clearcache')) {
            $menu->getChild('Maintenance')->addChild('Clear the cache', [
                'uri' => $this->urlGenerator->generate('bolt_clear_cache'),
                'extras' => [
                    'name' => $t->trans('caption.clear_cache'),
                    'icon' => 'fa-eraser',
                ],
            ]);
        }

        if ($this->authorizationChecker->isGranted('translation')) {
            $menu->getChild('Maintenance')->addChild('Translations', [
                'uri' => $this->urlGenerator->generate('translation_index'),
                'extras' => [
                    'name' => $t->trans('caption.translations'),
                    'icon' => 'fa-language',
                ],
            ]);
        }

        // Hide this menu item, unless we're on a "Git clone" install and user has 'kitchensink' permissions
        if (Version::installType() === 'Git clone' && $this->authorizationChecker->isGranted('kitchensink')) {
            $menu->getChild('Maintenance')->addChild('The Kitchensink', [
                'uri' => $this->urlGenerator->generate('bolt_kitchensink'),
                'extras' => [
                    'name' => $t->trans('caption.kitchensink'),
                    'icon' => 'fa-bath',
                ],
            ]);
        }

        if ($this->authorizationChecker->isGranted('about')) {
            $menu->getChild('Maintenance')->addChild('About Bolt', [
                'uri' => $this->urlGenerator->generate('bolt_about'),
                'extras' => [
                    'name' => $t->trans('caption.about_bolt'),
                    'icon' => 'fa-award',
                ],
            ]);
        }

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

        if ($this->authorizationChecker->isGranted('managefiles:files')) {
            $menu->getChild('File Management')->addChild('Uploaded files', [
                'uri' => $this->urlGenerator->generate('bolt_filemanager', ['location' => 'files']),
                'extras' => [
                    'name' => $t->trans('caption.uploaded_files'),
                    'icon' => 'fa-archive',
                ],
            ]);
        }

        if ($this->authorizationChecker->isGranted('managefiles:themes')) {
            $menu->getChild('File Management')->addChild('View/edit Templates', [
                'uri' => $this->urlGenerator->generate('bolt_filemanager', ['location' => 'themes']),
                'extras' => [
                    'name' => $t->trans('caption.view_edit_templates'),
                    'icon' => 'fa-scroll',
                ],
            ]);
        }

        // These 'container' menus can be empty due to permissions - remove them if this is the case
        foreach (['Configuration', 'Maintenance', 'File Management'] as $menuName) {
            if ($menu->getChild($menuName) !== null && count($menu->getChild($menuName)->getChildren()) === 0) {
                $menu->removeChild($menuName);
            }
        }

        return $menu;
    }

    private function addContentItems(MenuItem $menu): void
    {
        /** @var ContentType[] $contentTypes */
        $contentTypes = $this->config->get('contenttypes')->whereStrict('show_in_menu', true);

        foreach ($contentTypes as $contentType) {
            // add only if the user can or needs to access this contenttype (for view, edit, ...)
            if (! $this->authorizationChecker->isGranted(ContentVoter::CONTENT_MENU_LISTING, $contentType)) {
                continue;
            }
            $menu->addChild($contentType->getSlug(), [
                'uri' => $this->urlGenerator->generate('bolt_content_overview', ['contentType' => $contentType->getSlug()]),
                'extras' => [
                    'name' => $contentType['name'],
                    'singular_name' => $contentType['singular_name'],
                    'slug' => $contentType->getSlug(),
                    'singular_slug' => $contentType['singular_slug'],
                    'icon' => $contentType['icon_many'],
                    'link_new' => $this->authorizationChecker->isGranted(ContentVoter::CONTENT_CREATE, $contentType) ? $this->urlGenerator->generate('bolt_content_new', ['contentType' => $contentType->getSlug()]) : null,
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

        $slugify = new Slugify(['separator' => '-']);
        foreach ($contentTypes as $contentType) {
            if (! $this->authorizationChecker->isGranted(ContentVoter::CONTENT_VIEW, $contentType)) {
                continue;
            }

            $label = $contentType->get('show_in_menu') ?: $t->trans('caption.other_content');
            $icon = $icon ?? $contentType->get('icon_many');

            if (! $menu->getChild($label)) {
                // Add the top level item

                $icon = $contentType->get('icon_many');
                $slug = $slugify->slugify($label);

                $menu->addChild($label, [
                    'uri' => $this->urlGenerator->generate('bolt_menupage', ['slug' => $slug]),
                    'extras' => [
                        'name' => $label,
                        'icon' => $icon,
                        'slug' => $slug,
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
        // If we use `cache/list_format`, delegate it to that Helper
        if ($this->config->get('general/caching/list_format')) {
            $records = $this->listFormatHelper->getMenuLinks($contentType, self::MAX_LATEST_RECORDS, 'modified_at');
        } else {
            $records = $this->contentRepository->findLatest($contentType, 1, self::MAX_LATEST_RECORDS);
        }


        $result = [];

        /** @var Content|array $record */
        foreach ($records as $record) {
            try {
                if ($record instanceof Content) {
                    $additionalResult = [
                        'id' => $record->getId(),
                        'name' => $this->contentExtension->getTitle($record),
                        'editLink' => $this->contentExtension->getEditLink($record),
                        'icon' => $record->getContentTypeIcon(),
                    ];
                } else {
                    $definition =  $this->config->get('contenttypes/' . $contentType->getSlug());

                    $additionalResult = [
                        'id' => $record['id'],
                        'name' => $record['name'],
                        'editLink' => $record['link'],
                        'icon' => $definition['icon_one'] ?: $definition['icon_many'],
                    ];
                }

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
