<?php

declare(strict_types=1);

namespace Bolt\Configuration;

class FileLocations
{
    /** @var array */
    private $locations = [];

    public function __construct(
        private readonly Config $config
    ) {
        $this->initLocations();
    }

    public function get(string $locationName): ?FileLocation
    {
        if (array_key_exists($locationName, $this->locations)) {
            return $this->locations[$locationName];
        }

        return null;
    }

    private function initLocations(): void
    {
        $this->locations = [
            'config' => new FileLocation('config', 'Configuration files', $this->config->getPath('config'), true, 'cog'),
            'files' => new FileLocation('files', 'Content files', $this->config->getPath('files'), false, 'archive'),
            'themes' => new FileLocation('themes', 'Theme files', $this->config->getPath('themes'), false, 'scroll'),
        ];
    }
}
