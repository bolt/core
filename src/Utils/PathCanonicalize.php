<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Symfony\Component\Filesystem\Path;

class PathCanonicalize
{
    public static function canonicalize(string $basePath, string $filename, bool $returnRelative = false): string
    {
        $path = Path::canonicalize($basePath . DIRECTORY_SEPARATOR . $filename);

        $relativePath = Path::makeRelative($path, $basePath);

        // If the path is outside the `$basePath`, we do not allow it.
        if (mb_strpos($relativePath, '..') === 0) {
            throw new \Exception('You are not allowed to access path ' . $path);
        }

        return $returnRelative ? $relativePath : $path;
    }
}
