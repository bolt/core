<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Symfony\Contracts\Cache\CacheInterface;

final class CachedFrontendMenuBuilder implements FrontendMenuBuilderInterface
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

        return $this->cache->get($key, function () use ($name) {
            return $this->menuBuilder->buildMenu($name);
        });
    }
}
