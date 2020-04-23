<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class CachedBackendMenuBuilder implements BackendMenuBuilderInterface
{
    /** @var CacheInterface */
    private $cache;

    /** @var BackendMenuBuilderInterface */
    private $menuBuilder;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(BackendMenuBuilderInterface $menuBuilder, TagAwareCacheInterface $cache, RequestStack $requestStack)
    {
        $this->cache = $cache;
        $this->menuBuilder = $menuBuilder;
        $this->requestStack = $requestStack;
    }

    public function buildAdminMenu(): array
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $cacheKey = 'backendmenu_' . $locale;
        return $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->tag('backendmenu');

            return $this->menuBuilder->buildAdminMenu();
        });
    }
}
