<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Symfony\Component\Stopwatch\Stopwatch;

class StopwatchBackendMenuBuilder implements BackendMenuBuilderInterface
{
    /** @var BackendMenuBuilderInterface */
    private $menuBuilder;

    /** @var Stopwatch */
    private $stopwatch;

    public function __construct(BackendMenuBuilderInterface $menuBuilder, Stopwatch $stopwatch)
    {
        $this->menuBuilder = $menuBuilder;
        $this->stopwatch = $stopwatch;
    }

    public function buildAdminMenu(): array
    {
        $this->stopwatch->start('bolt.backendMenu');
        $menu = $this->menuBuilder->buildAdminMenu();
        $this->stopwatch->stop('bolt.backendMenu');

        return $menu;
    }
}
