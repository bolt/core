<?php

declare(strict_types=1);

namespace Bolt\Content;

use Bolt\Configuration\Config;
use Knp\Menu\FactoryInterface;

class MenuBuilder
{
    private $factory;

    private $config;

    /**
     * MenuBuilder constructor.
     *
     * @param Config $config
     */
    public function __construct(FactoryInterface $factory, Config $config)
    {
        $this->config = $config;
        $this->factory = $factory;
    }

    public function createSidebarMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Dashboard', ['uri' => 'homepage', 'extras' => [
            'name' => 'Dashboard',
            'icon_one' => 'fa-tachometer-alt',
            'link' => '/bolt/',
        ]]);

        $menu->addChild('Content', ['uri' => 'content', 'extras' => [
            'name' => 'Content',
            'type' => 'separator',
            'icon_one' => 'fa-file',
        ]]);

        foreach ($this->config->get('contenttypes') as $contenttype) {
            $menu->addChild($contenttype['name'], ['uri' => 'homepage', 'extras' => [
                'name' => $contenttype['name'],
                'icon_one' => $contenttype['icon_one'],
                'icon_many' => $contenttype['icon_many'],
                'link' => '/bolt/content/' . $contenttype['slug'],
                'contenttype' => $contenttype['slug'],
                'singleton' => $contenttype['singleton'],
                'active' => $contenttype['slug'] === 'pages' ? true : false,
            ]]);
        }

        $menu->addChild('Settings', ['uri' => 'settings', 'extras' => [
            'name' => 'Settings',
            'type' => 'separator',
            'icon_one' => 'fa-wrench',
        ]]);

        $menu->addChild('Configuration', ['uri' => 'configuration', 'extras' => [
            'name' => 'Configuration',
            'icon_one' => 'fa-flag',
            'link' => '/bolt/finder/config',
        ]]);

        $menu->addChild('Content Files', ['uri' => 'content-files', 'extras' => [
            'name' => 'Content Files',
            'icon_one' => 'fa-flag',
            'link' => '/bolt/finder/files',
        ]]);

        $menu->addChild('Theme Files', ['uri' => 'theme-files', 'extras' => [
            'name' => 'Theme Files',
            'icon_one' => 'fa-flag',
            'link' => '/bolt/finder/themes',
        ]]);

        $menu->addChild('Users', ['uri' => 'users', 'extras' => [
            'name' => 'Users',
            'icon_one' => 'fa-users',
            'link' => '/bolt/users',
        ]]);

        return $menu;
    }

    public function getMenu()
    {
        $menu = $this->createSidebarMenu()->getChildren();

        $menuData = [];

        foreach ($menu as $child) {
            $menuData[] = [
                'name' => $child->getLabel(),
                'icon_one' => $child->getExtra('icon_one') ? $child->getExtra('icon_one') : null,
                'icon_many' => $child->getExtra('icon_many') ? $child->getExtra('icon_many') : null,
                'link' => $child->getExtra('link') ? $child->getExtra('link') : null,
                'contenttype' => $child->getExtra('contenttype') ? $child->getExtra('contenttype') : null,
                'singleton' => $child->getExtra('singleton') ? $child->getExtra('singleton') : null,
                'type' => $child->getExtra('type') ? $child->getExtra('type') : null,
                'active' => $child->getExtra('active') ? $child->getExtra('active') : null,
            ];
        }

        return $menuData;
    }
}
