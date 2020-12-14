<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Twig\Environment;

interface FrontendMenuBuilderInterface
{
    public function buildMenu(Environment $twig, ?string $name): array;
}
