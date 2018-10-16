<?php

declare(strict_types=1);

namespace Bolt\Twig\Extension;

use Bolt\Content\FieldFactory;
use Bolt\Content\MenuBuilder;
use Bolt\Twig\Runtime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContentHelperExtension extends AbstractExtension
{
    /** @var MenuBuilder */
    private $menuBuilder;

    public function __construct(MenuBuilder $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sidebarmenu', [$this, 'sidebarmenu']),
            new TwigFunction('fieldfactory', [$this, 'fieldfactory']),
            new TwigFunction('selectoptionsfromarray', [Runtime\ContentHelperRuntime::class, 'selectoptionsfromarray']),
        ];
    }

    public function sidebarmenu()
    {
        $menu = $this->menuBuilder->getMenu();

        return json_encode($menu);
    }

    public function fieldfactory($definition, $name = null)
    {
        $field = FieldFactory::get($definition['type']);
        $field->setName($name);
        $field->setDefinition($definition, $name);

        return $field;
    }
}
