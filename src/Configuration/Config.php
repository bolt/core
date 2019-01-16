<?php

declare(strict_types=1);

namespace Bolt\Configuration;

use Bolt\Common\Arr;
use Bolt\Configuration\Parser\ContentTypesParser;
use Bolt\Configuration\Parser\GeneralParser;
use Bolt\Configuration\Parser\TaxonomyParser;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\Stopwatch\Stopwatch;
use Tightenco\Collect\Support\Collection;

class Config
{
    /** @var Collection */
    protected $data;

    /** @var PathResolver */
    private $pathResolver;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var FilesystemCache */
    private $cache;

    /** @var array */
    private $timestamps = [];

    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
        $this->cache = new FilesystemCache();
        $this->pathResolver = new PathResolver(dirname(dirname(__DIR__)), []);
        $this->data = $this->getConfig();
    }

    private function getConfig(): Collection
    {
        $this->stopwatch->start('bolt.parseconfig');

        if ($this->validCache()) {
            $data = $this->getCache();
        } else {
            $data = $this->parseConfig();
            $this->setCache();
        }

        $this->stopwatch->stop('bolt.parseconfig');

        return $data;
    }

    private function validCache(): bool
    {
        if (! $this->cache->has('config_cache') || ! $this->cache->has('config_timestamps')) {
            return false;
        }

        $timestamps = $this->cache->get('config_timestamps');

        foreach ($timestamps as $filename => $timestamp) {
            if (filemtime($filename) > $timestamp) {
                return false;
            }
        }

        return true;
    }

    private function getCache(): Collection
    {
        return $this->cache->get('config_cache');
    }

    private function setCache(): void
    {
        $this->cache->set('config_cache', $this->data);
        $this->cache->set('config_timestamps', $this->timestamps);
    }

    /**
     * Load the configuration from the various YML files.
     */
    private function parseConfig(): Collection
    {
        $general = new GeneralParser();

        $config = collect([
            'general' => $general->parse(),
        ]);

        $this->data = $config;

        $taxonomy = new TaxonomyParser();
        $config['taxonomies'] = $taxonomy->parse();

        $contentTypes = new ContentTypesParser($this->get('general')['accept_file_types']);
        $config['contenttypes'] = $contentTypes->parse();

        //'menu' => $this->parseConfigYaml('menu.yml'),
        //'routing' => $this->parseConfigYaml('routing.yml'),
        //'permissions' => $this->parseConfigYaml('permissions.yml'),
        //'extensions' => $this->parseConfigYaml('extensions.yml'),

        $this->setTimestamps($general, $taxonomy, $contentTypes);

        return $config;
    }

    private function setTimestamps(...$configs): void
    {
        $this->timestamps = [];

        foreach ($configs as $config) {
            foreach ($config->getFilenames() as $file) {
                $this->timestamps[$file] = filemtime($file);
            }
        }

        $envFilename = dirname(dirname(__DIR__)) . '/.env';
        if (file_exists($envFilename)) {
            $this->timestamps[$envFilename] = filemtime($envFilename);
        }
    }

    /**
     * Get a config value, using a path.
     *
     * For example:
     * $var = $config->get('general/wysiwyg/ck/contentsCss');
     *
     * @param string|array|bool $default
     */
    public function get(string $path, $default = null)
    {
        return Arr::get($this->data, $path, $default);
    }

    public function getPath(string $path, bool $absolute = true, $additional = null): string
    {
        return $this->pathResolver->resolve($path, $absolute, $additional);
    }

    public function getPaths(): Collection
    {
        return $this->pathResolver->resolveAll();
    }

    public function getMediaTypes(): Collection
    {
        return collect(['png', 'jpg', 'jpeg', 'gif', 'svg', 'pdf', 'mp3', 'tiff']);
    }
}
