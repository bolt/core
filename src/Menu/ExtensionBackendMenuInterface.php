<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Knp\Menu\MenuItem;

interface ExtensionBackendMenuInterface
{
    public function addItems(MenuItem $menu): void;
}
