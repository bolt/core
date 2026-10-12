<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Bolt\Configuration\Config;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

class ThumbnailCacheClearer
{
    public function __construct(
        private readonly Config $config,
        private readonly LoggerInterface $logger
    ) {
    }

    public function run(): bool
    {
        $path = $this->config->getPath('thumbs');

        $finder = new Finder();
        $filesystem = new Filesystem();

        $finder->directories()->in($path)->depth(0);

        $success = true;

        foreach ($finder as $folder) {
            $absPath = $folder->getRealPath();

            try {
                $filesystem->remove($absPath);
            } catch (IOException) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Removes the thumbnails of a file, or of all files in a folder, e.g. after it was deleted. The
     * web server serves saved thumbnails straight from disk, so they would stay public otherwise.
     *
     * A thumbnail is saved at `thumbs/<parameters>/<path relative to its location>`, and Glide caches
     * it at `<cache>/thumbnails/<location relative to the web root>/<path relative to its location>`.
     * Any path inside the web root can be the location (e.g. `files`, `theme` or `web`), so this
     * removes the path relative to each location that contains it, in every parameters folder. That
     * can also remove the thumbnail of a file with the same relative path in another location, which
     * is then generated again.
     */
    public function clearFor(string $path): bool
    {
        $webPath = $this->config->getPath('web');
        $thumbsPath = $this->config->getPath('thumbs');
        $glideCachePath = $this->config->getPath('cache', true, 'thumbnails');
        $path = Path::canonicalize($path);

        $locations = $this->config->getPaths()
            ->filter(fn (string $location): bool => Path::isBasePath($webPath, $location) && Path::isBasePath($location, $path) && $location !== $path)
            ->unique();

        $parameterFolders = is_dir($thumbsPath) ? (new Finder())->directories()->in($thumbsPath)->depth(0) : [];
        $targets = [];

        foreach ($locations as $location) {
            $relativePath = Path::makeRelative($path, $location);
            $targets[] = Path::join($glideCachePath, Path::makeRelative($location, $webPath), $relativePath);

            foreach ($parameterFolders as $folder) {
                $targets[] = Path::join($folder->getPathname(), $relativePath);
            }
        }

        try {
            (new Filesystem())->remove(array_unique($targets));
        } catch (IOException $e) {
            $this->logger->warning('Could not remove the thumbnails of {path}: {message}', [
                'path' => $path,
                'message' => $e->getMessage(),
            ]);

            return false;
        }

        return true;
    }
}
