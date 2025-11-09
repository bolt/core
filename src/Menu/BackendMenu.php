<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Bolt\Configuration\Config;
use Bolt\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final readonly class BackendMenu implements BackendMenuBuilderInterface
{
    private string $backendUrl;

    public function __construct(
        private BackendMenuBuilder $menuBuilder,
        private TagAwareCacheInterface $cache,
        private RequestStack $requestStack,
        private Stopwatch $stopwatch,
        private Config $config,
        private Security $security,
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
            $username = $user->getUserIdentifier();
        } else {
            $username = '';
        }

        if ($user instanceof User) {
            $username = $user->getUserIdentifier();
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
