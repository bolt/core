<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Cocur\Slugify\Slugify;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;
use Tightenco\Collect\Support\Collection;

trait ConfigTrait
{
    /** @var Collection */
    private $config;

    /**
     * Returns the classname of the Extension
     */
    public function getClass(): string
    {
        return static::class;
    }

    public function getConfig(): Collection
    {
        if ($this->config === null) {
            $this->initializeConfig();
        }

        return $this->config;
    }

    private function initializeConfig(): void
    {
        $config = [];

        $filenames = $this->getConfigFilenames();

        if (! is_readable($filenames['main']) && is_readable($this->getDefaultConfigFilename())) {
            $filesystem = new Filesystem();
            $filesystem->copy($this->getDefaultConfigFilename(), $filenames['main']);
        }

        $yamlParser = new Parser();

        foreach ($filenames as $filename) {
            if (is_readable($filename)) {
                $config = array_merge($config, $yamlParser->parseFile($filename) ?? [] );
            }
        }

        $this->config = new Collection($config);
    }

    public function getConfigFilenames(): array
    {
        $slugify = new Slugify();
        $baseClassPath = mb_substr($this->getClass(), 0, mb_strrpos($this->getClass(), '\\'));
        $baseName = $slugify->slugify($baseClassPath);
        $path = $this->getBoltConfig()->getPath('extensions_config');

        return [
            'main' => sprintf('%s%s%s.yaml', $path, DIRECTORY_SEPARATOR, $baseName),
            'local' => sprintf('%s%s%s_local.yaml', $path, DIRECTORY_SEPARATOR, $baseName),
        ];
    }

    public function hasConfigFilenames(): array
    {
        $result = [];

        foreach ($this->getConfigFilenames() as $filename) {
            if (is_readable($filename)) {
                $result[] = basename(dirname($filename)) . DIRECTORY_SEPARATOR . basename($filename);
            }
        }

        return $result;
    }

    private function getDefaultConfigFilename(): string
    {
        $reflection = new \ReflectionClass($this);

        return sprintf(
            '%s%s%s%s%s',
            dirname(dirname($reflection->getFilename())),
            DIRECTORY_SEPARATOR,
            'config',
            DIRECTORY_SEPARATOR,
            'config.yaml'
        );
    }
}
