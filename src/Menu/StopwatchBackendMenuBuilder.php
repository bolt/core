<?php

declare(strict_types=1);

namespace Bolt\Menu;

/**
 * @deprecated since Bolt 5.1. This class is just an empty wrapper around BackendMenu now. Use that class instead
 */
final class StopwatchBackendMenuBuilder implements BackendMenuBuilderInterface
{
    /** @var BackendMenu */
    private $menuBuilder;

    public function __construct(BackendMenu $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    public function buildAdminMenu(): array
    {
        return $this->menuBuilder->buildAdminMenu();
        ;
    }
}
