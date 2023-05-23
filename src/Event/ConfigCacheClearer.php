<?php

declare(strict_types=1);

namespace Bolt\Event;

use Bolt\Configuration\Config;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class ConfigCacheClearer implements CacheClearerInterface
{
    /** @var CacheInterface */
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function clear(string $cacheDir): void
    {
        $this->cache->delete(Config::CACHE_KEY);
        $this->cache->delete(Config::OPTIONS_CACHE_KEY);
    }
}
