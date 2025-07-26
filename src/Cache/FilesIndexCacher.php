<?php

namespace Bolt\Cache;

use Bolt\Utils\FilesIndex;
use Illuminate\Support\Collection;

class FilesIndexCacher extends FilesIndex implements CachingInterface
{
    use CachingTrait;

    public const CACHE_CONFIG_KEY = 'files_index';

    public function get(string $path, string $type, string $baseUrlPath, string $baseFilePath): Collection
    {
        $this->setCacheTags(['fileslisting']);
        $this->setCacheKey([$path, $type]);

        /** @phpstan-ignore argument.type */
        return $this->execute([parent::class, __FUNCTION__], [$path, $type, $baseUrlPath, $baseFilePath]);
    }
}
