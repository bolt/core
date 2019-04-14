<?php

declare(strict_types=1);

namespace Bolt\Configuration;

final class FileLocations
{
    /** @var array */
    private $locations = [];

    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->initLocations();
    }

    public function get(string $area): ?FileLocation
    {
        if (array_key_exists($area, $this->locations)) {
            return $this->locations[$area];
        }

        return null;
    }

    private function initLocations(): void
    {
        $this->locations = [
            'config' => new FileLocation('config', 'Configuration files', $this->config->getPath('config'), true),
            'files' => new FileLocation('files', 'Content files', $this->config->getPath('files'), false),
            'themes' => new FileLocation('themes', 'Theme files', $this->config->getPath('themes'), false),
        ];
    }
}
