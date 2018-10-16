<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;
use Tightenco\Collect\Support\Collection;

class BaseParser
{
    /** @var FileLocator */
    protected $fileLocator;

    /** @var array */
    protected $accept_file_types;

    public function __construct($accept_file_types = [])
    {
        $this->accept_file_types = $accept_file_types;
        $configDirectories = [dirname(dirname(dirname(__DIR__))) . '/config/bolt'];
        $this->fileLocator = new FileLocator($configDirectories);
    }

    /**
     * Read and parse a YAML configuration file.
     *
     * @param string $filename The name of the YAML file to read
     *
     * @return Collection
     */
    protected function parseConfigYaml($filename): Collection
    {
        try {
            $filename = $this->fileLocator->locate($filename, null, true);
        } catch (FileNotFoundException $e) {
            return collect([]);
        }

        $yaml = Yaml::parseFile($filename);

        // Unset the repeated nodes key after parse
        unset($yaml['__nodes']);

        return collect($yaml);
    }
}
