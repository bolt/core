<?php

declare(strict_types=1);

namespace Bolt\Configuration;

use Bolt\Common\Arr;
use Bolt\Configuration\Parser\ContentTypesParser;
use Bolt\Configuration\Parser\GeneralParser;
use Bolt\Configuration\Parser\TaxonomyParser;
use Tightenco\Collect\Support\Collection;

class Config
{
    /** @var Collection */
    protected $data;

    /** @var PathResolver */
    private $pathResolver;

    public function __construct()
    {
        $this->pathResolver = new PathResolver(dirname(dirname(__DIR__)), []);

        $this->data = $this->parseConfig();
    }

    /**
     * Load the configuration from the various YML files.
     */
    public function parseConfig(): Collection
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

        return $config;
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
