<?php

namespace Bolt\Cache;

use Bolt\Utils\FilesIndex;
use Tightenco\Collect\Support\Collection;

class FilesIndexCacher extends FilesIndex implements CachingInterface
{
    use CachingTrait;

    public const CACHE_CONFIG_KEY = 'files_index';

    public function get(string $path, string $type, string $basePath): Collection
    {
        $this->setCacheTags(['fileslisting']);
        $this->setCacheKey([$path, $type]);

        return $this->execute([parent::class, __FUNCTION__], [$path, $type, $basePath]);
    }
}