<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Menu\BackendMenuBuilderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BackendMenuExtension extends AbstractExtension
{
    /** @var BackendMenuBuilderInterface */
    private $menuBuilder;

    public function __construct(BackendMenuBuilderInterface $menuBuilder)
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
        ];
    }

    public function getAdminMenu(): array
    {
        return $this->menuBuilder->buildAdminMenu();
    }
}
