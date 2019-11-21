<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Symfony\Contracts\Cache\CacheInterface;

final class CachedBackendMenuBuilder implements BackendMenuBuilderInterface
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
        return $this->cache->get('backendmenu', function () {
            return $this->menuBuilder->buildAdminMenu();
        });
    }
}
