<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Menu\BackendMenuBuilderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BackendMenuExtension extends AbstractExtension
{
    public function __construct(
        private readonly BackendMenuBuilderInterface $menuBuilder
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('admin_menu_array', $this->getAdminMenuArray(...)),
        ];
    }

    public function getAdminMenuArray(): array
    {
        return $this->menuBuilder->buildAdminMenu();
    }
}
