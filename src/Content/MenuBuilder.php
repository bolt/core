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
                'icon_one' => 'fa-tachometer-alt',
                'link' => '/bolt/',
            ],
        ];

        $menu[] = [
            'name' => 'Content',
            'type' => 'separator',
            'icon_one' => 'fa-file',
        ];

        foreach ($this->config->get('contenttypes') as $contenttype) {
            $menu[] = [
                'name' => $contenttype['name'],
                'icon_one' => $contenttype['icon_one'],
                'icon_many' => $contenttype['icon_many'],
                'link' => '/bolt/content/' . $contenttype['slug'],
                'contenttype' => $contenttype['slug'],
                'singleton' => $contenttype['singleton'],
                'active' => $contenttype['slug'] == 'pages' ? true : false,
            ];
        }

        $menu[] = [
            'name' => 'Settings',
            'type' => 'separator',
            'icon_one' => 'fa-wrench',
        ];

        $menu[] = [
            'name' => 'Configuration',
            'icon_one' => 'fa-flag',
            'link' => '/bolt/finder/config',
        ];
        $menu[] = [
            'name' => 'Content Files',
            'icon_one' => 'fa-flag',
            'link' => '/bolt/finder/files',
        ];
        $menu[] = [
            'name' => 'Theme Files',
            'icon_one' => 'fa-flag',
            'link' => '/bolt/finder/themes',
        ];
        $menu[] = [
                'name' => 'Users',
                'icon_one' => 'fa-users',
                'link' => '/bolt/users',
        ];

        return $menu;
    }
}
