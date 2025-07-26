<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Bolt\Configuration\Config;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ThumbnailCacheClearer
{
    public function __construct(
        private readonly Config $config
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
}
