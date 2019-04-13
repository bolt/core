<?php

declare(strict_types=1);

namespace Bolt\Configuration;

use Tightenco\Collect\Support\Collection;

final class FileLocations
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

    public function get(string $area): ?FileLocation
    {
        return $this->areas->get($area);
    }

    private function initAreas(): void
    {
        $this->areas = new Collection([
            'config' => new FileLocation('config', 'Configuration files', $this->config->getPath('config'), true),
            'files' => new FileLocation('files', 'Content files', $this->config->getPath('files'), false),
            'themes' => new FileLocation('themes', 'Theme files', $this->config->getPath('themes'), false),
        ]);
    }
}
