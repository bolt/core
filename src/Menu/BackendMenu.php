<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Bolt\Configuration\Config;
use Bolt\Entity\User;
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

    /** @var Config */
    private $config;

    /** @var User */
    private $user;

    public function __construct(
        BackendMenuBuilder $menuBuilder,
        TagAwareCacheInterface $cache,
        RequestStack $requestStack,
        Stopwatch $stopwatch,
        Config $config,
        User $user
    )
    {
        $this->cache = $cache;
        $this->menuBuilder = $menuBuilder;
        $this->requestStack = $requestStack;
        $this->stopwatch = $stopwatch;
        $this->config = $config;
        $this->user = $user;
    }

    public function buildAdminMenu(): array
    {
        $this->stopwatch->start('bolt.backendMenu');

        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $username = $this->user->getUsername();
        $cacheKey = 'bolt.backendMenu_' . $locale . '_' . $username;

        $menu = $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter($this->config->get('general/caching/backend_menu'));
            $item->tag('backendmenu');

            return $this->menuBuilder->buildAdminMenu();
        });

        $this->stopwatch->stop('bolt.backendMenu');

        return $menu;
    }
}
