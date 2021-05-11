<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Symfony\Component\Stopwatch\Stopwatch;
use Twig\Environment;

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

    public function buildMenu(Environment $twig, ?string $name = null): array
    {
        $this->stopwatch->start('bolt.frontendMenu');
        $menu = $this->menuBuilder->buildMenu($twig, $name);
        $this->stopwatch->stop('bolt.frontendMenu');

        return $menu;
    }
}
