<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Twig\Environment;

/**
 * @deprecated since Bolt 5.1. This class is just an empty wrapper around FrontendMenu now. Use that class instead
 */
final class CachedFrontendMenuBuilder implements FrontendMenuBuilderInterface
{
    public function __construct(
        private readonly FrontendMenu $menuBuilder
    ) {
    }

    public function buildMenu(Environment $twig, ?string $name = null): array
    {
        return $this->menuBuilder->buildMenu($twig, $name);
    }
}
