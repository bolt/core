<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

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
        $permissionConfig = $this->parseConfigYaml($this->getInitialFilename());

        return new Collection($permissionConfig);
    }
}
