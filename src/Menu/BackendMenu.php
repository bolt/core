<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Bolt\Configuration\Config;
use Bolt\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class BackendMenu implements BackendMenuBuilderInterface
{
    private readonly string $backendUrl;

    public function __construct(
        private readonly BackendMenuBuilder $menuBuilder,
        private readonly TagAwareCacheInterface $cache,
        private readonly RequestStack $requestStack,
        private readonly Stopwatch $stopwatch,
        private readonly Config $config,
        private readonly Security $security,
        string $backendUrl = 'bolt'
    ) {
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

        $menu = $this->cache->get($cacheKey, function (ItemInterface $item): array {
            $item->expiresAfter((int) $this->config->get('general/caching/backend_menu'));
            $item->tag('backendmenu');

            return $this->menuBuilder->buildAdminMenu();
        });

        $this->stopwatch->stop('bolt.backendMenu');

        return $menu;
    }
}
