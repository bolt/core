<?php

declare(strict_types=1);

namespace Bolt\Configuration;

use Bolt\Collection\DeepCollection;
use Bolt\Common\Arr;
use Bolt\Configuration\Parser\BaseParser;
use Bolt\Configuration\Parser\ContentTypesParser;
use Bolt\Configuration\Parser\GeneralParser;
use Bolt\Configuration\Parser\MenuParser;
use Bolt\Configuration\Parser\PermissionsParser;
use Bolt\Configuration\Parser\TaxonomyParser;
use Bolt\Configuration\Parser\ThemeParser;
use Bolt\Controller\Backend\ClearCacheController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Tightenco\Collect\Support\Collection;
use Webimpress\SafeWriter\FileWriter;

class Config
{
    public const CACHE_KEY = 'config_cache';
    public const OPTIONS_CACHE_KEY = 'options_preparse';

    /** @var Collection */
    protected $data;

    /** @var PathResolver */
    private $pathResolver;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var string */
    private $publicFolder;

    /** @var CacheInterface */
    private $cache;

    /** @var string */
    private $projectDir;

    /** @var string */
    private $locales;

    /** @var string */
    private $defaultLocale;

    /** @var ClearCacheController */
    private $clearCacheController;

    /** @var KernelInterface */
    private $kernel;

    public function __construct(string $locales, string $defaultLocale, Stopwatch $stopwatch, string $projectDir, CacheInterface $cache, string $publicFolder, ClearCacheController $clearCacheController, KernelInterface $kernel)
    {
        $this->locales = $locales;
        $this->stopwatch = $stopwatch;
        $this->cache = $cache;
        $this->projectDir = $projectDir;
        $this->publicFolder = $publicFolder;
        $this->defaultLocale = $defaultLocale;
        $this->clearCacheController = $clearCacheController;
        $this->kernel = $kernel;

        $this->data = $this->getConfig();

        // @todo PathResolver shouldn't be part of Config. Refactor to separate class
        $this->pathResolver = new PathResolver($projectDir, $this->get('general/theme'), $this->publicFolder);
    }

    private function getConfig(): Collection
    {
        $this->stopwatch->start('bolt.parseconfig');

        [$data, $timestamps] = $this->getCache();

        // Verify if timestamps are unchanged. If not, invalidate cache.
        foreach ($timestamps as $filename => $timestamp) {
            if (file_exists($filename) === false || filemtime($filename) > $timestamp) {
                $this->cache->delete(self::CACHE_KEY);
                [$data] = $this->getCache();

                // Clear the entire cache in order to re-generate %bolt.requirement.contenttypes%
                $this->clearCacheController->clearcache($this->kernel);
            }
        }

        $this->stopwatch->stop('bolt.parseconfig');

        return $data;
    }

    private function getCache(): array
    {
        return $this->cache->get(self::CACHE_KEY, function () {
            return $this->parseConfig();
        });
    }

    /**
     * Load the configuration from the various YML files.
     */
    private function parseConfig(): array
    {
        $general = new GeneralParser($this->projectDir);

        $config = new Collection([
            'general' => $general->parse(),
        ]);

        $taxonomy = new TaxonomyParser($this->projectDir);
        $config['taxonomies'] = $taxonomy->parse();

        $contentTypes = new ContentTypesParser($this->projectDir, $config->get('general'), $this->defaultLocale, $this->locales);
        $config['contenttypes'] = $contentTypes->parse();

        $menu = new MenuParser($this->projectDir);
        $config['menu'] = $menu->parse();

        // If we're parsing the config, we'll also need to pre-initialise
        // the PathResolver, because we need to know the theme path.
        $this->pathResolver = new PathResolver($this->projectDir, $config->get('general')->get('theme'), $this->publicFolder);

        $theme = new ThemeParser($this->projectDir, $this->getPath('theme'));
        $config['theme'] = $theme->parse();

        $permissions = new PermissionsParser($this->projectDir);
        $config['permissions'] = $permissions->parse();

        // @todo Add these config files if needed, or refactor them out otherwise
        //'permissions' => $this->parseConfigYaml('permissions.yml'),
        //'extensions' => $this->parseConfigYaml('extensions.yml'),

        $timestamps = $this->getConfigFilesTimestamps($general, $taxonomy, $contentTypes, $menu, $theme, $permissions);

        return [
            DeepCollection::deepMake($config),
            $timestamps,
        ];
    }

    private function getConfigFilesTimestamps(BaseParser ...$configs): array
    {
        $timestamps = [];

        foreach ($configs as $config) {
            foreach ($config->getParsedFilenames() as $file) {
                $timestamps[$file] = filemtime($file);
            }
        }

        return array_merge($timestamps, $this->getEnvFilesTimestamps());
    }

    private function getEnvFilesTimestamps(): array
    {
        // For filenames see:
        // https://symfony.com/doc/current/configuration.html#configuring-environment-variables-in-env-files
        $envFilenames = [
            '.env',
            '.env.local',
            '.env.' . $this->kernel->getEnvironment(),
            '.env.' . $this->kernel->getEnvironment() . '.local',
        ];

        $envTimestamps = [];

        foreach ($envFilenames as $envFilename) {
            $envFilenamePath = $this->projectDir . '/' . $envFilename;
            if (file_exists($envFilenamePath)) {
                $envTimestamps[$envFilenamePath] = filemtime($envFilenamePath);
            }
        }

        return $envTimestamps;
    }

    /**
     * Get a config value, using a path.
     *
     * For example:
     * $var = $config->get('general/wysiwyg/ck/contentsCss');
     *
     * @param string|array|bool|int|Collection $default
     */
    public function get(string $path, $default = null)
    {
        $value = Arr::get($this->data, $path, $default);

        // Basic $_ENV parser, for values like `%env(FOO_BAR)%`
        if (is_string($value) && preg_match('/%env\(([A-Z0-9_]+)\)%/', $value, $matches)) {
            if (isset($_ENV[$matches[1]])) {
                $value = $_ENV[$matches[1]];
            }

            if (empty($value) && getenv($matches[1])) {
                $value = getenv($matches[1]);
            }
        }

        return $value;
    }

    public function has(string $path): bool
    {
        return Arr::has($this->data, $path);
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
        return new Collection($this->get('general/accept_media_types'));
    }

    public function getContentType(string $name): ?Collection
    {
        $name = trim($name);

        if ($this->has('contenttypes/' . $name)) {
            return $this->get('contenttypes/' . $name);
        }

        /** @var Collection $cts */
        $cts = $this->get('contenttypes');

        foreach (['singular_slug', 'name', 'singular_name'] as $key) {
            if ($cts->firstWhere($key, $name)) {
                return $cts->firstWhere($key, $name);
            }
        }

        return null;
    }

    public function getTaxonomy(string $name): ?Collection
    {
        $name = trim($name);

        if ($this->has('taxonomies/' . $name)) {
            return $this->get('taxonomies/' . $name);
        }

        /** @var Collection $taxos */
        $taxos = $this->get('taxonomies');

        foreach (['slug', 'singular_slug', 'name', 'singular_name'] as $key) {
            if ($taxos->firstWhere($key, $name)) {
                return $taxos->firstWhere($key, $name);
            }
        }

        return null;
    }

    public function getFileTypes(): Collection
    {
        return new Collection($this->get('general/accept_file_types'));
    }

    public function getMaxUpload(): int
    {
        return min(
            $this->convertPHPSizeToBytes(ini_get('post_max_size')),
            $this->convertPHPSizeToBytes(ini_get('upload_max_filesize')),
            $this->convertPHPSizeToBytes($this->get('general/accept_upload_size', '8M'))
        );
    }

    public function getMaxUploadDescription(): string
    {
        return sprintf('This value is the minimum of these constraints:<br> <strong>Bolt\'s <code>config.yaml</code></strong>:<br>
<code>accept_upload_size</code>: <code>%s</code><br><br>
<strong>PHP\'s <code>php.ini</code></strong>:<br>
<code>post_max_size</code>: <code>%s</code><br> <code>upload_max_filesize</code>: <code>%s</code>',
            $this->get('general/accept_upload_size', '8M'),
            ini_get('post_max_size'),
            ini_get('upload_max_filesize')
        );
    }

    /**
     * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
     */
    public static function convertPHPSizeToBytes(string $size): int
    {
        $suffix = mb_strtoupper(mb_substr($size, -1));
        if (! in_array($suffix, ['P', 'T', 'G', 'M', 'K'], true)) {
            return (int) $size;
        }
        $value = (int) mb_substr($size, 0, -1);
        switch ($suffix) {
            case 'P':
                $value *= 1024;
                // no break
            case 'T':
                $value *= 1024;
                // no break
            case 'G':
                $value *= 1024;
                // no break
            case 'M':
                $value *= 1024;
                // no break
            case 'K':
                $value *= 1024;

                break;
        }

        return $value;
    }
}
