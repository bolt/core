<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Symfony\Component\Stopwatch\Stopwatch;

final class StopwatchFrontendMenuBuilder implements FrontendMenuBuilderInterface
{
    /** @var FrontendMenuBuilderInterface */
    private $menuBuilder;

    /** @var Stopwatch */
    private $stopwatch;

    public function __construct(FrontendMenuBuilderInterface $menuBuilder, Stopwatch $stopwatch)
    {
        $this->menuBuilder = $menuBuilder;
        $this->stopwatch = $stopwatch;
    }

    public function buildMenu(?string $name = null): array
    {
        $this->stopwatch->start('bolt.frontendMenu');
        $menu = $this->menuBuilder->buildMenu($name);
        $this->stopwatch->stop('bolt.frontendMenu');

        return $menu;
    }
}
