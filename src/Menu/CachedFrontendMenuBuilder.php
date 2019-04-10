<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Bolt\Collection\DeepCollection;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class CachedFrontendMenuBuilder
{
    /** @var CacheInterface */
    private $cache;

    /** @var FrontendMenuBuilder */
    private $menuBuilder;

    /** @var Stopwatch */
    private $stopwatch;

    public function __construct(CacheInterface $cache, FrontendMenuBuilder $menuBuilder, Stopwatch $stopwatch)
    {
        $this->cache = $cache;
        $this->menuBuilder = $menuBuilder;
        $this->stopwatch = $stopwatch;
    }

    public function buildMenu(?string $name = ''): ?DeepCollection
    {
        $key = 'frontendmenu_' . ($name ?: 'default');

        if ($this->cache->has($key)) {
            $menu = $this->cache->get($key);
        } else {
            $this->stopwatch->start('bolt.frontendMenu');

            $menu = $this->menuBuilder->buildMenu($name);

            $this->cache->set($key, $menu);

            $this->stopwatch->stop('bolt.frontendMenu');
        }

        return $menu;
    }
}
