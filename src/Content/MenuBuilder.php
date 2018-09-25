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

        foreach ($this->config->get('contenttypes') as $contenttype) {
            $menu[] = [
                'name' => $contenttype['name'],
                'icon' => 'fa-leaf',
                'link' => '/bolt/content/' . $contenttype['slug'],
                'contenttype' => $contenttype['slug'],
            ];
        }

        $menu[] = [
                'name' => 'Settings',
                'icon' => 'fa-flag',
                'link' => '/bolt/settings',
        ];
        $menu[] = [
                'name' => 'Users',
                'icon' => 'fa-users',
                'link' => '/bolt/users',
        ];

        return $menu;
    }
}
