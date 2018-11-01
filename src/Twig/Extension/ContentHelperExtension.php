<?php

declare(strict_types=1);

namespace Bolt\Twig\Extension;

use Bolt\Content\FieldFactory;
use Bolt\Content\MenuBuilder;
use Bolt\Entity\Content;
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
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFunction('sidebarmenu', [$this, 'sidebarmenu']),
            new TwigFunction('fieldfactory', [$this, 'fieldfactory']),
            new TwigFunction('selectoptionsfromarray', [Runtime\ContentHelperRuntime::class, 'selectoptionsfromarray']),
            new TwigFunction('icon', [$this, 'icon'], $safe),
        ];
    }

    public function sidebarmenu($pretty = false)
    {
        $menu = $this->menuBuilder->getMenu();

        $options = $pretty ? JSON_PRETTY_PRINT : 0;

        return json_encode($menu, $options);
    }

    public function fieldfactory($name, $definition)
    {
        $field = FieldFactory::get($definition['type']);
        $field->setName($name);
        $field->setDefinition($name, $definition);

        return $field;
    }

    public function icon($record, $icon = 'question-circle')
    {
        if ($record instanceof Content) {
            $icon = $record->getDefinition()->get('icon_one') ?: $record->getDefinition()->get('icon_many');
        }

        $icon = str_replace('fa-', '', $icon);

        return "<i class='fas mr-2 fa-$icon'></i>";
    }
}
