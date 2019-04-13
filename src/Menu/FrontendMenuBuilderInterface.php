<?php

declare(strict_types=1);

namespace Bolt\Menu;

interface FrontendMenuBuilderInterface
{
    public function buildMenu(?string $name): array;
}
