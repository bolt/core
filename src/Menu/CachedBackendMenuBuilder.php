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

    public function getMenu(): array
    {
        if ($this->cache->has('backendmenu')) {
            $menu = $this->cache->get('backendmenu');
        } else {
            $this->stopwatch->start('bolt.sidebarMenu');

            $menu = $this->menuBuilder->getMenu();
            $this->cache->set('backendmenu', $menu);

            $this->stopwatch->stop('bolt.sidebarMenu');
        }

        return $menu;
    }

    public function getMenuJson(bool $jsonPrettyPrint = false): string
    {
        $menu = $this->getMenu();
        $options = $jsonPrettyPrint ? JSON_PRETTY_PRINT : 0;

        return json_encode($menu, $options);
    }
}
