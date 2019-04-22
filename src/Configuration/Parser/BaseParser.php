<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Bolt\Configuration\PathResolver;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
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
    protected function parseConfigYaml(string $filename, $ignoreMissing = false): Collection
    {
        try {
            if (! is_readable($filename)) {
                $filename = $this->fileLocator->locate($filename, null, true);
            }
        } catch (FileLocatorFileNotFoundException $e) {
            if ($ignoreMissing) {
                return new Collection([]);
            }

            // If not $ignoreMissing, we throw the exception regardless.
            throw $e;
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

    public function getFilename()
    {
        return $this->filename;
    }

    public function getFilenameLocalOverrides()
    {
        return preg_replace('/([a-z0-9_-]+).(ya?ml)$/i', '$1_local.$2', $this->filename);
    }

    abstract public function parse(): Collection;
}
