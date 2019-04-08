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
            new TwigFunction('admin_menu', [$this, 'getAdminMenu']),
            new TwigFunction('admin_menu_json', [$this, 'getAdminMenuJson']),
        ];
    }

    public function getAdminMenu(): array
    {
        return $this->menuBuilder->getMenu();
    }

    public function getAdminMenuJson($jsonPrettyPrint = false): string
    {
        return $this->menuBuilder->getMenuJson($jsonPrettyPrint);
    }
}
