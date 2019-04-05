<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Menu\CachedBackendMenuBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BackendMenuExtension extends AbstractExtension
{
    /** @var CachedBackendMenuBuilder */
    private $menuBuilder;

    public function __construct(CachedBackendMenuBuilder $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sidebar_menu', [$this, 'getSidebarMenu']),
        ];
    }

    public function getSidebarMenu($jsonPrettyPrint = false): string
    {
        return $this->menuBuilder->getMenu($jsonPrettyPrint);
    }
}
