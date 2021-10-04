<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Twig\Environment;

final class CachedFrontendMenuBuilder implements FrontendMenuBuilderInterface
{
    /** @var TagAwareCacheInterface */
    private $cache;

    /** @var FrontendMenuBuilderInterface */
    private $menuBuilder;

    /** @var Request */
    private $request;

    /** @var Stopwatch */
    private $stopwatch;

    public function __construct(FrontendMenuBuilderInterface $menuBuilder, TagAwareCacheInterface $cache, RequestStack $requestStack, Stopwatch $stopwatch)
    {
        $this->cache = $cache;
        $this->menuBuilder = $menuBuilder;
        $this->request = $requestStack->getCurrentRequest();
        $this->stopwatch = $stopwatch;
    }

    public function buildMenu(Environment $twig, ?string $name = null): array
    {
        $this->stopwatch->start('bolt.frontendMenu');

        $key = 'frontendmenu_' . ($name ?: 'main') . '_' . $this->request->getLocale();

        $menu = $this->cache->get($key, function (ItemInterface $item) use ($name, $twig) {
            $item->tag('frontendmenu');

            return $this->menuBuilder->buildMenu($twig, $name);
        });

        $this->stopwatch->stop('bolt.frontendMenu');

        return $menu;
    }
}
