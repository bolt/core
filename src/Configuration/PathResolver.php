<?php

declare(strict_types=1);

namespace Bolt\Configuration;

use Bolt\Exception\ConfigurationException;
use Symfony\Component\Filesystem\Path;
use Tightenco\Collect\Support\Collection;

/**
 * A class to resolve and manage paths. Paths defined here are allowed to have variables within them.
 * For example: "files" folder is within the web directory so it is defined as "%web%/files". This allows
 * the web directory to be changed and the files path does not have to be redefined.
 *
 * @author Carson Full <carsonfull@gmail.com>
 */
class PathResolver
{
    /** @var array */
    protected $paths = [];

    /** @var array */
    private $resolving = [];

    /**
     * Default paths for Bolt installation.
     */
    public static function defaultPaths(string $public): array
    {
        return [
            'site' => '.',
            'var' => '%site%/var',
            'cache' => '%var%/cache',
            'config' => '%site%/config',
            'database' => '%var%/data',
            'extensions' => '%site%/extensions',
            'extensions_config' => '%config%/extensions',
            'web' => '%site%/' . $public,
            'files' => '%web%/files',
            'themes' => '%web%/theme',
            'thumbs' => '%web%/thumbs',
            'bolt_assets' => '%web%/bolt-public',
        ];
    }

    /**
     * @param string $root the root path which must be absolute
     *
     * @throws ConfigurationException
     */
    public function __construct(string $root, string $themeName = '', string $public = 'public')
    {
        $paths = $this->defaultPaths($public);

        foreach ($paths as $name => $path) {
            $this->define($name, $path);
        }

        $root = Path::canonicalize($root);

        if (Path::isRelative($root)) {
            throw new ConfigurationException('Root path must be absolute.');
        }

        $this->paths['root'] = $root;

        $this->paths['theme'] = '%themes%/' . $themeName;
    }

    /**
     * Define a path, or really an alias/variable.
     *
     * @throws ConfigurationException
     */
    public function define(string $name, string $path): void
    {
        if (mb_strpos($path, "%{$name}%") !== false) {
            throw new ConfigurationException('Paths cannot reference themselves.');
        }

        $this->paths[$name] = $path;
    }

    /**
     * Resolve a path.
     *
     * Examples:
     *  - `%web%/files` - A path with variables.
     *  - `files` - A previously defined variable.
     *  - `foo/bar` - A relative path that will be resolved against the root path.
     *  - `/tmp` - An absolute path will be returned as is.
     *
     * @param bool $absolute if the path is relative, resolve it against the root path
     *
     * @throws ConfigurationException
     */
    public function resolve(string $path, bool $absolute = true, $additional = null): string
    {
        if (isset($this->paths[$path])) {
            $path = $this->paths[$path];
        }

        $path = preg_replace_callback('#%(.+)%#', function ($match) use ($path) {
            $alias = $match[1];

            if (! isset($this->paths[$alias])) {
                throw new ConfigurationException("Failed to resolve path. Alias %{$alias}% is not defined.");
            }

            // absolute if alias is at start of path
            $absolute = mb_strpos($path, "%{$alias}%") === 0;

            if (isset($this->resolving[$alias])) {
                throw new ConfigurationException('Failed to resolve path. Infinite recursion detected.');
            }

            $this->resolving[$alias] = true;

            try {
                return $this->resolve($alias, $absolute);
            } finally {
                unset($this->resolving[$alias]);
            }
        }, $path);

        if ($absolute && Path::isRelative($path)) {
            $path = Path::makeAbsolute($path, $this->paths['root']);
        }

        if (! empty($additional)) {
            $path .= \DIRECTORY_SEPARATOR . implode(\DIRECTORY_SEPARATOR, (array) $additional);
        }

        // Make sure we don't have lingering unneeded dir-seperators
        return Path::canonicalize($path);
    }

    public function resolveAll(): Collection
    {
        $paths = [];
        foreach ($this->paths as $name => $path) {
            $paths[$name] = $this->resolve($path);
        }

        return new Collection($paths);
    }

    /**
     * Returns the raw path definition for the name given.
     */
    public function raw(string $name): ?string
    {
        return $this->paths[$name] ?? null;
    }

    /**
     * Returns all path names and their raw definitions.
     */
    public function rawAll(): array
    {
        $paths = $this->paths;
        unset($paths['root']);

        return $paths;
    }

    /**
     * Returns the names of all paths.
     */
    public function names(): array
    {
        return array_keys($this->rawAll());
    }
}
