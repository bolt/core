<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Twig\Environment;

/**
 * @deprecated since Bolt 5.1. This class is just an empty wrapper around FrontendMenu now. Use that class instead
 */
final class CachedFrontendMenuBuilder implements FrontendMenuBuilderInterface
{
    /** @var FrontendMenuBuilderInterface */
    private $menuBuilder;

    public function __construct(FrontendMenu $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    public function buildMenu(Environment $twig, ?string $name = null): array
    {
        return $this->menuBuilder->buildMenu($twig, $name);
    }
}
