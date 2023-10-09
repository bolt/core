<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Bolt\Configuration\Config;
use Bolt\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
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

    /** @var Security */
    private $security;

    /** @var string */
    private $backendUrl = '/bolt';

    public function __construct(
        BackendMenuBuilder $menuBuilder,
        TagAwareCacheInterface $cache,
        RequestStack $requestStack,
        Stopwatch $stopwatch,
        Config $config,
        Security $security,
        string $backendUrl = 'bolt'
    ) {
        $this->cache = $cache;
        $this->menuBuilder = $menuBuilder;
        $this->requestStack = $requestStack;
        $this->stopwatch = $stopwatch;
        $this->config = $config;
        $this->security = $security;
        $this->backendUrl = preg_replace('/[^\pL\d,]+/u', '', $backendUrl);
    }

    public function buildAdminMenu(): array
    {
        $this->stopwatch->start('bolt.backendMenu');

        $locale = $this->requestStack->getCurrentRequest()->getLocale();

        /** @var User|null $user */
        $user = $this->security->getUser();

        if ($user instanceof User) {
            $username = $user->getUsername();
        } else {
            $username = '';
        }

        if ($user instanceof User) {
            $username = $user->getUsername();
        } else {
            $username = '';
        }

        $cacheKey = 'bolt.backendMenu_' . $locale . '_' . $this->backendUrl . '_' . $username;

        $menu = $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter((int) $this->config->get('general/caching/backend_menu'));
            $item->tag('backendmenu');

            return $this->menuBuilder->buildAdminMenu();
        });

        $this->stopwatch->stop('bolt.backendMenu');

        return $menu;
    }
}
