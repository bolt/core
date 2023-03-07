<?php

namespace Bolt\Cache;

use Bolt\Configuration\Config;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

trait CachingTrait
{
    /** @var TagAwareCacheInterface */
    private $cache;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var string */
    private $cacheKey = '';

    /** @var array */
    private $cacheTags = [];

    /** @var Config */
    private $config;

    /**
     * @required
     */
    public function setCache(TagAwareCacheInterface $cache, Stopwatch $stopwatch): void
    {
        $this->cache = $cache;
        $this->stopwatch = $stopwatch;
    }

    /**
     * @required
     */
    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }

    public function setCacheKey(array $tokens): void
    {
        $this->cacheKey = self::CACHE_CONFIG_KEY . '_' . md5(implode('', $tokens));
    }

    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    public function setCacheTags(array $tags): void
    {
        foreach ($tags as $key => $tag) {
            $tags[$key] = preg_replace('/[^\pL\d,]+/u', '', $tag);
        }

        $this->cacheTags = $tags;
    }

    public function getCacheTags(): array
    {
        return $this->cacheTags;
    }

    public function execute(callable $fn, array $params = [])
    {
        $key = $this->getCacheKey();

        $this->stopwatch->start('bolt.cache.' . $key);

        if ($this->isCachingEnabled()) {
            $results = $this->cache->get($key, function (ItemInterface $item) use ($fn, $params) {
                $item->expiresAfter($this->getExpiresAfter());
                $item->tag($this->getCacheTags());

                return call_user_func_array($fn, $params);
            });
        } else {
            $results = call_user_func_array($fn, $params);
        }

        $this->stopwatch->stop('bolt.cache.' . $key);

        return $results;
    }

    private function isCachingEnabled(): bool
    {
        $configKey = $this->config->get('general/caching/' . self::CACHE_CONFIG_KEY);

        return $configKey > 0;
    }

    private function getExpiresAfter(): int
    {
        return (int) $this->config->get('general/caching/' . self::CACHE_CONFIG_KEY, 3600);
    }

    /**
     * Make sure something like `(pages,entries)` becomes an array like ['pages', 'entries']
     */
    private function getTags(string $contentTypeSlug): array
    {
        $tags = explode(',', $contentTypeSlug);

        $tags = array_map(function($t) {
            return preg_replace('/[^\pL\d,]+/u', '', $t);
        }, $tags);

        return $tags;
    }
}
