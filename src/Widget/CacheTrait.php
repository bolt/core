<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Psr\SimpleCache\CacheInterface;

trait CacheTrait
{
    /** @var CacheInterface */
    private $cache;

    /** @var string */
    private $key;

    public function setCacheInterface(CacheInterface $cache): void
    {
        $this->cache = $cache;
        $this->key = $this->createKey();
    }

    public function cachedInvoke(): string
    {
        if ($this->isCached()) {
            return $this->getFromCache();
        }

        /** @var WidgetInterface $this */
        $output = $this();

        $this->setToCache($output);

        return $output;
    }

    private function setToCache(string $output): void
    {
        $this->cache->set($this->key, $output, $this->getCacheDuration());
    }

    private function getFromCache(): string
    {
        return $this->cache->get($this->key);
    }

    private function isCached(): bool
    {
        return $this->cache->has($this->key);
    }

    private function createKey()
    {
        return $this->getName() . $this->getTarget() . $this->getZone() . $this->getPriority();
    }
}
