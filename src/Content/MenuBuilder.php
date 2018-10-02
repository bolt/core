<?php

declare(strict_types=1);

namespace Bolt\Content;

use Bolt\Configuration\Config;

class MenuBuilder
{
    private $config;

    /**
     * MenuBuilder constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function get()
    {
        $menu = [
            [
                'name' => 'Dashboard',
                'icon' => 'fa-tachometer-alt',
                'link' => '/bolt/',
            ],
        ];

        $menu[] = [
            'name' => 'Content',
            'type' => 'separator',
            'icon' => 'fa-file',
        ];

        foreach ($this->config->get('contenttypes') as $contenttype) {
            $menu[] = [
                'name' => $contenttype['name'],
                'icon' => 'fa-leaf',
                'link' => '/bolt/content/' . $contenttype['slug'],
                'contenttype' => $contenttype['slug'],
                'singleton' => $contenttype['singleton'],
            ];
        }

        $menu[] = [
            'name' => 'Settings',
            'type' => 'separator',
            'icon' => 'fa-wrench',
        ];

        $menu[] = [
            'name' => 'Configuration',
            'icon' => 'fa-flag',
            'link' => '/bolt/finder/config',
        ];
        $menu[] = [
            'name' => 'Content Files',
            'icon' => 'fa-flag',
            'link' => '/bolt/finder/files',
        ];
        $menu[] = [
            'name' => 'Theme Files',
            'icon' => 'fa-flag',
            'link' => '/bolt/finder/themes',
        ];
        $menu[] = [
                'name' => 'Users',
                'icon' => 'fa-users',
                'link' => '/bolt/users',
        ];

        return $menu;
    }
}
