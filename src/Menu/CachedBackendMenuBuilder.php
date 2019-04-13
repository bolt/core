<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Psr\SimpleCache\CacheInterface;

class CachedBackendMenuBuilder implements BackendMenuBuilderInterface
{
    /** @var CacheInterface */
    private $cache;

    /** @var BackendMenuBuilderInterface */
    private $menuBuilder;

    public function __construct(BackendMenuBuilderInterface $menuBuilder, CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->menuBuilder = $menuBuilder;
    }

    public function buildAdminMenu(): array
    {
        if ($this->cache->has('backendmenu')) {
            $menu = $this->cache->get('backendmenu');
        } else {
            $menu = $this->menuBuilder->buildAdminMenu();
            $this->cache->set('backendmenu', $menu);
        }

        return $menu;
    }
}
