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

    /** @var string */
    protected $initialFilename;

    /** @var string[] */
    protected $parsedFilenames = [];

    public function __construct(string $projectDir, string $initialFilename)
    {
        $this->fileLocator = new FileLocator([$projectDir . '/' . $this->getProjectConfigDir()]);
        $this->pathResolver = new PathResolver(dirname(dirname(dirname(__DIR__))));
        $this->initialFilename = $initialFilename;
    }

    /**
     * Read and parse a YAML configuration file.
     *
     * If filename doesn't exist and/or isn't readable, we attempt to locate it
     * in our config folder. This way you can pass in either an absolute
     * filename or simply 'menu.yaml'.
     */
    protected function parseConfigYaml(string $filename, bool $ignoreMissing = false): Collection
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

        $this->parsedFilenames[] = $filename;

        // Unset the repeated nodes key after parse
        unset($yaml['__nodes']);

        return new Collection($yaml);
    }

    public function getParsedFilenames(): array
    {
        return $this->parsedFilenames;
    }

    public function getInitialFilename()
    {
        return $this->initialFilename;
    }

    protected function getProjectConfigDir(): string
    {
        $projectConfigDir = $_ENV['BOLT_CONFIG_FOLDER'] ?? null;

        if (empty($projectConfigDir) && getenv('BOLT_CONFIG_FOLDER')) {
            $projectConfigDir = getenv('BOLT_CONFIG_FOLDER');
        }

        return $projectConfigDir ?? 'config/bolt';
    }

    public function getFilenameLocalOverrides()
    {
        return preg_replace('/([a-z0-9_-]+).(ya?ml)$/i', '$1_local.$2', $this->initialFilename);
    }

    abstract public function parse(): Collection;
}
