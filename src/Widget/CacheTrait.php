<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Exception\WidgetException;
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

    public function __invoke(array $params = []): string
    {
        if (!$this->cache instanceof CacheInterface) {
            throw new WidgetException('Widget of class '.self::class.' is not initialised properly. Make sure the Widget `implements CacheAware`.');
        }

        if ($this->isCached()) {
            return $this->getFromCache();
        }

        $output = $this->run($params);

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
