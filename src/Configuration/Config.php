<?php

declare(strict_types=1);

namespace Bolt\Configuration;

use Bolt\Common\Arr;
use Bolt\Configuration\Parser\ContentTypesParser;
use Bolt\Configuration\Parser\GeneralParser;
use Bolt\Configuration\Parser\TaxonomyParser;
use Symfony\Component\Config\FileLocator;
use Tightenco\Collect\Support\Collection;
use Webmozart\PathUtil\Path;

class Config
{
    /** @var array */
    protected $data;

    /** @var FileLocator */
    private $fileLocator;

    /** @var PathResolver */
    private $pathResolver;

    public function __construct()
    {
        $this->pathResolver = new PathResolver(dirname(dirname(__DIR__)), []);

        $configDirectories = [dirname(dirname(__DIR__)) . '/config/bolt'];
        $this->fileLocator = new FileLocator($configDirectories);

        // $this->cacheFile = $this->app['filesystem']->getFile('cache://config-cache.json');

        $data = null;

        // $data = $this->loadCache();
        if ($data === null) {
            $data = $this->parseConfig();

            // If we have to reload the config, we will also want to make sure
            // the DB integrity is checked.
            // $this->app['schema.timer']->setCheckRequired();
        }

        $this->data = $data;
    }

    /**
     * Load the configuration from the various YML files.
     *
     * @return array
     */
    public function parseConfig()
    {
        $general = new GeneralParser();

        $config = collect([
            'general' => $general->parse(),
        ]);

        $this->data = $config;

        $taxonomy = new TaxonomyParser();
        $config['taxonomies'] = $taxonomy->parse();

        $contenttypes = new ContentTypesParser($this->get('general')['accept_file_types']);
        $config['contenttypes'] = $contenttypes->parse();

        //'menu' => $this->parseConfigYaml('menu.yml'),
        //'routing' => $this->parseConfigYaml('routing.yml'),
        //'permissions' => $this->parseConfigYaml('permissions.yml'),
        //'extensions' => $this->parseConfigYaml('extensions.yml'),

        return $config;
    }

    /**
     * Get a config value, using a path.
     *
     * For example:
     * $var = $config->get('general/wysiwyg/ck/contentsCss');
     *
     * @param string            $path
     * @param string|array|bool $default
     *
     * @return mixed
     */
    public function get(string $path, $default = null)
    {
        return Arr::get($this->data, $path, $default);
    }

    /**
     * @param string $path
     * @param bool   $absolute
     * @param mixed  $additional
     *
     * @return string
     */
    public function getPath(string $path, bool $absolute = true, $additional = null): string
    {
        return $this->pathResolver->resolve($path, $absolute, $additional);
    }

    /**
     * @return Collection
     */
    public function getPaths(): Collection
    {
        return $this->pathResolver->resolveAll();
    }

    /**
     * @return Collection
     */
    public function getMediaTypes(): Collection
    {
        return collect(['png', 'jpg', 'jpeg', 'gif', 'svg', 'pdf', 'mp3', 'tiff']);
    }
}
