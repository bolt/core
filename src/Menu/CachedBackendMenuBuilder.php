<?php

declare(strict_types=1);

namespace Bolt\Menu;

/**
 * @deprecated since Bolt 5.1. This class is just an empty wrapper around BackendMenu now. Use that class instead
 */
final readonly class CachedBackendMenuBuilder implements BackendMenuBuilderInterface
{
    public function __construct(
        private BackendMenu $menuBuilder
    ) {
    }

    public function buildAdminMenu(): array
    {
        return $this->menuBuilder->buildAdminMenu();
    }
}
