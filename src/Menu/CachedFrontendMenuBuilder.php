<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Psr\SimpleCache\CacheInterface;

class CachedFrontendMenuBuilder implements FrontendMenuBuilderInterface
{
    /** @var CacheInterface */
    private $cache;

    /** @var FrontendMenuBuilderInterface */
    private $menuBuilder;

    public function __construct(FrontendMenuBuilderInterface $menuBuilder, CacheInterface $cache)
    {
        $this->cache = $cache;
        $this->menuBuilder = $menuBuilder;
    }

    public function buildMenu(?string $name = null): array
    {
        $key = 'frontendmenu_' . ($name ?: 'main');

        if ($this->cache->has($key)) {
            $menu = $this->cache->get($key);
        } else {
            $menu = $this->menuBuilder->buildMenu($name);
            $this->cache->set($key, $menu);
        }

        return $menu;
    }
}
