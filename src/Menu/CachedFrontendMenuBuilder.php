<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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

    public function __construct(FrontendMenuBuilderInterface $menuBuilder, TagAwareCacheInterface $cache, RequestStack $requestStack)
    {
        $this->cache = $cache;
        $this->menuBuilder = $menuBuilder;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function buildMenu(Environment $twig, ?string $name = null): array
    {
        $key = 'frontendmenu_' . ($name ?: 'main') . '_' . $this->request->getLocale();

        return $this->cache->get($key, function (ItemInterface $item) use ($name, $twig) {
            $item->tag('frontendmenu');

            return $this->menuBuilder->buildMenu($twig, $name);
        });
    }
}
