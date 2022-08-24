<?php

namespace Bolt\Utils;

use Bolt\Configuration\Config;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use Tightenco\Collect\Support\Collection;

class FilesIndex
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function get(string $path, string $type, string $baseUrlPath, string $baseFilePath): Collection
    {
        if ($type === 'images') {
            $glob = sprintf('*.{%s}', $this->config->getMediaTypes()->implode(','));
        } else {
            $glob = null;
        }

        $files = [];

        foreach (self::findDirectories($path) as $dir) {
            $files[] = [
                'group' => 'directories',
                'value' => $dir->getPathname(),
                'text' => $dir->getFilename(),
                'base_url_path' => $baseUrlPath
            ];
        }

        foreach (self::findFiles($path, $glob) as $file) {
            $files[] = [
                'group' => basename($baseFilePath),
                'value' => $path . '/' . $file->getRelativePathname(),
                'text' => $file->getFilename(),
                'base_url_path' => $baseUrlPath
            ];
        }

        return new Collection($files);
    }

    private function findFiles(string $path, string $glob = null): Finder
    {
        $finder = new Finder();
        $finder->in($path)->depth('0')->sortByType()->files();

        if ($glob) {
            $finder->name($glob);
        }

        return $finder;
    }

    private function findDirectories(string $path): Finder
    {
        $finder = new Finder();
        $finder->in($path)->depth('0')->sortByType()->directories();

        return $finder;
    }
}
