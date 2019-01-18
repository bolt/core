<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Bolt\Configuration\PathResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Yaml\Yaml;
use Tightenco\Collect\Support\Collection;

class BaseParser
{
    /** @var FileLocator */
    protected $fileLocator;

    /** @var PathResolver */
    protected $pathResolver;

    /** @var string[] */
    protected $filenames = [];

    public function __construct()
    {
        $configDirectories = [dirname(dirname(dirname(__DIR__))) . '/config/bolt'];
        $this->fileLocator = new FileLocator($configDirectories);
        $this->pathResolver = new PathResolver(dirname(dirname(dirname(__DIR__))), []);
    }

    /**
     * Read and parse a YAML configuration file.
     */
    protected function parseConfigYaml(string $filename): Collection
    {
        try {
            $filename = $this->fileLocator->locate($filename, null, true);
        } catch (FileNotFoundException $e) {
            return new Collection();
        }

        $yaml = Yaml::parseFile($filename);

        $this->filenames[] = $filename;

        // Unset the repeated nodes key after parse
        unset($yaml['__nodes']);

        return collect($yaml);
    }

    public function getFilenames(): array
    {
        return $this->filenames;
    }
}
