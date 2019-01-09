<?php

declare(strict_types=1);

namespace Bolt\Configuration;

use Tightenco\Collect\Support\Collection;

final class Areas
{
    /** @var Collection */
    private $areas;

    /** @var Config */
    private $config;

    /**
     * Areas constructor.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->initAreas();
    }

    /**
     * @return Collection|string
     */
    public function get(string $area, ?string $key = null)
    {
        if ($key) {
            return $this->areas->get($area)[$key];
        }

        return collect($this->areas->get($area));
    }

    private function initAreas(): void
    {
        $this->areas = collect([
            'config' => [
                'key' => 'config',
                'name' => 'Configuration files',
                'basepath' => $this->config->getPath('config'),
                'show_all' => true,
            ],
            'files' => [
                'key' => 'files',
                'name' => 'Content files',
                'basepath' => $this->config->getPath('files'),
                'show_all' => false,
            ],
            'themes' => [
                'key' => 'themes',
                'name' => 'Theme files',
                'basepath' => $this->config->getPath('themes'),
                'show_all' => false,
            ],
        ]);
    }
}
