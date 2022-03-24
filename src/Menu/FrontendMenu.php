<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Bolt\Configuration\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Twig\Environment;


final class FrontendMenu implements FrontendMenuBuilderInterface
{
    /** @var TagAwareCacheInterface */
    private $cache;

    /** @var FrontendMenuBuilder */
    private $menuBuilder;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var Config */
    private $config;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(FrontendMenuBuilder $menuBuilder, TagAwareCacheInterface $cache, RequestStack $requestStack, Stopwatch $stopwatch, Config $config)
    {
        $this->cache = $cache;
        $this->menuBuilder = $menuBuilder;
        $this->stopwatch = $stopwatch;
        $this->config = $config;
        $this->requestStack = $requestStack;
    }

    public function buildMenu(Environment $twig, ?string $name = null): array
    {
        $this->stopwatch->start('bolt.frontendMenu');

        $key = 'bolt.frontendMenu_' . ($name ?: 'main') . '_' . $this->requestStack->getCurrentRequest()->getLocale();

        $menu = $this->cache->get($key, function (ItemInterface $item) use ($name, $twig) {
            $item->expiresAfter($this->config->get('general/caching/frontend_menu'));
            $item->tag('frontendmenu');

            return $this->menuBuilder->buildMenu($twig, $name);
        });

        $this->stopwatch->stop('bolt.frontendMenu');

        return $menu;
    }
}
