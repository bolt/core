<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Bolt\Configuration\PathResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;
use Tightenco\Collect\Support\Collection;

abstract class BaseParser
{
    /** @var FileLocator */
    protected $fileLocator;

    /** @var PathResolver */
    protected $pathResolver;

    /** @var string[] */
    protected $filenames = [];

    /** @var string */
    protected $filename;

    public function __construct(string $filename)
    {
        $configDirectories = [dirname(dirname(dirname(__DIR__))) . '/config/bolt'];
        $this->fileLocator = new FileLocator($configDirectories);
        $this->pathResolver = new PathResolver(dirname(dirname(dirname(__DIR__))), []);
        $this->filename = $filename;
    }

    /**
     * Read and parse a YAML configuration file.
     *
     * If filename doesn't exist and/or isn't readable, we attempt to locate it
     * in our config folder. This way you can pass in either  an absolute
     * filename or simply 'menu.yaml'.
     */
    protected function parseConfigYaml(string $filename): Collection
    {
        if (! is_readable($filename)) {
            $filename = $this->fileLocator->locate($filename, null, true);
        }

        $yaml = Yaml::parseFile($filename);

        $this->filenames[] = $filename;

        // Unset the repeated nodes key after parse
        unset($yaml['__nodes']);

        return new Collection($yaml);
    }

    public function getFilenames(): array
    {
        return $this->filenames;
    }

    abstract public function parse(): Collection;
}
