<?php

namespace Bolt\Cache;

use Bolt\Configuration\Config;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

trait CachingTrait
{
    /** @var TagAwareCacheInterface */
    private $cache;

    /** @var string */
    private $cacheKey;

    /** @var Config */
    private $config;

    /**
     * @required
     */
    public function setCache(TagAwareCacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @required
     */
    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function setCacheKey(string $key): void
    {
        $this->cacheKey = $key;
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey ?? '';
    }

    public function execute(callable $fn, array $params = [])
    {
        if ($this->isCachingEnabled()) {
            return $this->cache->get($this->getCacheKey(), function(ItemInterface $item) use ($fn, $params) {
                $item->expiresAfter($this->getExpiresAfter());

                return call_user_func_array($fn, $params);
            });
        }

        return $fn($params);
    }

    private function isCachingEnabled(): bool
    {
        return $this->config->get('general/caching/' . self::CACHE_CONFIG_KEY, true);
    }

    private function getExpiresAfter(): int
    {
        return $this->config->get('general/caching/expires_after', 3600);
    }
}
