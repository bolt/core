<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Tightenco\Collect\Support\Collection;

class MenuParser extends BaseParser
{
    /** @var array */
    private $baseItem = [];

    public function __construct()
    {
        $this->baseItem = [
            'label' => '',
            'title' => '',
            'path' => '',
            'link' => '',
            'class' => '',
            'submenu' => null,
            'uri' => '',
            'current' => false,
        ];

        parent::__construct();
    }

    /**
     * Read and parse the taxonomy.yml configuration file.
     */
    public function parse(): Collection
    {
        $menuYaml = $this->parseConfigYaml('menu.yaml');

        $menu = [];

        foreach ($menuYaml as $key => $items) {
            if (is_array($items)) {
                $menu[$key] = $this->parseItems($items);
            }
        }

        return new Collection($menu);
    }

    private function parseItems(array $items)
    {
        $menu = [];

        foreach ($items as $item) {
            $item = array_merge($this->baseItem, $item);

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $item['submenu'] = $this->parseItems($item['submenu']);
            }

            $menu[] = $item;
        }

        return $menu;
    }
}
