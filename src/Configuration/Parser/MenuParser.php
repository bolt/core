<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Illuminate\Support\Collection;

class MenuParser extends BaseParser
{
    /** @var array */
    private $itemBase = [];

    public function __construct(string $projectDir, string $initialFilename = 'menu.yaml')
    {
        $this->itemBase = [
            'label' => '',
            'title' => '',
            'link' => '',
            'class' => '',
            'submenu' => null,
            'uri' => '',
        ];

        parent::__construct($projectDir, $initialFilename);
    }

    /**
     * Read and parse the taxonomy.yml configuration file.
     */
    public function parse(): Collection
    {
        $menuYaml = $this->parseConfigYaml($this->getInitialFilename());

        $menu = [];

        foreach ($menuYaml as $key => $items) {
            if (is_array($items)) {
                $menu[$key] = $this->parseItems($items);
            }
        }

        return new Collection($menu);
    }

    private function parseItems(array $items): array
    {
        $menu = [];

        foreach ($items as $item) {
            $item = array_merge($this->itemBase, (array) $item);

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                $item['submenu'] = $this->parseItems($item['submenu']);
            }

            // Backwards compatibility for `path`
            if (! empty($item['path']) && $item['link'] === '') {
                $item['link'] = $item['path'];
            }

            $menu[] = $item;
        }

        return $menu;
    }
}
