<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class CachedBackendMenuBuilder
{
    /** @var CacheInterface */
    private $cache;

    /** @var BackendMenuBuilder */
    private $menuBuilder;

    /** @var Stopwatch */
    private $stopwatch;

    public function __construct(CacheInterface $cache, BackendMenuBuilder $menuBuilder, Stopwatch $stopwatch)
    {
        $this->cache = $cache;
        $this->menuBuilder = $menuBuilder;
        $this->stopwatch = $stopwatch;
    }

    public function getMenu(bool $jsonPrettyPrint = false): string
    {
        $key = 'backendmenu_' . (int) $pretty;

        if ($this->cache->has($key)) {
            $menu = $this->cache->get($key);
        } else {
            $this->stopwatch->start('bolt.sidebarMenu');

            $menuArray = $this->menuBuilder->getMenu();
            $options = $jsonPrettyPrint ? JSON_PRETTY_PRINT : 0;
            $menu = json_encode($menuArray, $options);

            $this->cache->set($key, $menu);

            $this->stopwatch->stop('bolt.sidebarMenu');
        }

        return $menu;
    }
}
