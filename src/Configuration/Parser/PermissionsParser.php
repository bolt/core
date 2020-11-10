<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Tightenco\Collect\Support\Collection;

class PermissionsParser extends BaseParser
{
    /** @var string */
    private $path;

    public function __construct(string $projectDir, string $filename = 'permissions.yaml')
    {
        $this->path = $path;

        parent::__construct($projectDir, $filename);
    }

    /**
     * Read and parse the theme.yml configuration file.
     */
    public function parse(): Collection
    {
        $theme = $this->parseConfigYaml($this->path . '/theme.yaml', true);

        return new Collection($theme);
    }
}
