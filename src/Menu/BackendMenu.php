<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class BackendMenu implements BackendMenuBuilderInterface
{
    /** @var CacheInterface */
    private $cache;

    /** @var BackendMenuBuilder */
    private $menuBuilder;

    /** @var RequestStack */
    private $requestStack;

    /** @var Stopwatch */
    private $stopwatch;

    public function __construct(BackendMenuBuilder $menuBuilder, TagAwareCacheInterface $cache, RequestStack $requestStack, Stopwatch $stopwatch)
    {
        $this->cache = $cache;
        $this->menuBuilder = $menuBuilder;
        $this->requestStack = $requestStack;
        $this->stopwatch = $stopwatch;
    }

    public function buildAdminMenu(): array
    {
        $this->stopwatch->start('bolt.backendMenu');

        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $cacheKey = 'backendmenu_' . $locale;

        $menu = $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->tag('backendmenu');

            return $this->menuBuilder->buildAdminMenu();
        });

        $this->stopwatch->stop('bolt.backendMenu');

        return $menu;
    }
}
