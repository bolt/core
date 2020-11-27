<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Bolt\Common\Arr;
use Tightenco\Collect\Support\Collection;

class PermissionsParser extends BaseParser
{
    public function __construct(string $projectDir, string $filename = 'permissions.yaml')
    {
        parent::__construct($projectDir, $filename);
    }

    /**
     * Read and parse the permissions configuration file.
     */
    public function parse(): Collection
    {
        $defaultConfig = $this->getDefaultConfig();
        $yamlConfig = $this->parseConfigYaml($this->getInitialFilename());
        $permissionConfig = Arr::replaceRecursive($defaultConfig, $yamlConfig);

        return new Collection($permissionConfig);
    }

    protected function getDefaultConfig(): array
    {
        // TODO PERMISSIONS add more defaults
        return [
            'assignable_roles' => ['ROLE_DEVELOPER', 'ROLE_ADMIN', 'ROLE_CHIEF_EDITOR', 'ROLE_EDITOR', 'ROLE_USER'],
            'assignable_roles_unchecked' => []
        ];
    }
}
