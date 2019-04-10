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
        return $this->menuBuilder->buildMenu();
    }

    public function getAdminMenuJson($jsonPrettyPrint = false): string
    {
        $menu = $this->menuBuilder->buildMenu();

        $options = $jsonPrettyPrint ? JSON_PRETTY_PRINT : 0;

        return json_encode($menu, $options);
    }
}
