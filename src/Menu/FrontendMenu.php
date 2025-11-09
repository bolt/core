<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Bolt\Configuration\Config;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Twig\Environment;

final readonly class FrontendMenu implements FrontendMenuBuilderInterface
{
    public function __construct(
        private FrontendMenuBuilder $menuBuilder,
        private TagAwareCacheInterface $cache,
        private RequestStack $requestStack,
        private Stopwatch $stopwatch,
        private Config $config
    ) {
    }

    public function buildMenu(Environment $twig, ?string $name = null): array
    {
        $this->stopwatch->start('bolt.frontendMenu');

        $key = 'bolt.frontendMenu_' . ($name ?: 'main') . '_' . $this->requestStack->getCurrentRequest()->getLocale();

        $menu = $this->cache->get($key, function (ItemInterface $item) use ($name, $twig): array {
            $item->expiresAfter($this->config->get('general/caching/frontend_menu'));
            $item->tag('frontendmenu');

            return $this->menuBuilder->buildMenu($twig, $name);
        });

        $this->stopwatch->stop('bolt.frontendMenu');

        return $menu;
    }
}
