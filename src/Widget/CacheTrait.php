<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Exception\WidgetException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

trait CacheTrait
{
    /** @var CacheInterface */
    private $cache;

    /** @var string */
    private $key;

    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
        $this->key = $this->createKey();
    }

    public function __invoke(array $params = []): ?string
    {
        if (! $this->cache instanceof CacheInterface) {
            throw new WidgetException('Widget of class ' . self::class . ' is not initialised properly. Make sure the Widget `implements CacheAwareInterface`.');
        }

        $result = $this->cache->get(
            $this->key,
            function (ItemInterface $item) use ($params) {
                $item->expiresAfter($this->getCacheDuration());

                return $this->run($params);
            }
        );

        if (is_array($result)) {
            $result = implode('', $result);
        }

        return $result;
    }

    private function createKey()
    {
        return sprintf('%s-%s-%s', $this->getSlug(), $this->getZone(), $this->getCacheDuration());
    }
}
