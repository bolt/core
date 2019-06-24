<?php

declare(strict_types=1);

namespace Bolt\Menu;

interface BackendMenuBuilderInterface
{
    public function buildAdminMenu(): array;
}
