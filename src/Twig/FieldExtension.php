<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Field;
use Tightenco\Collect\Support\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FieldExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('label', [$this, 'getLabel']),
            new TwigFilter('type', [$this, 'getType']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('field_factory', [$this, 'fieldFactory']),
        ];
    }

    public function fieldFactory(string $name, ?Collection $definition = null): Field
    {
        if ($definition === null || $definition->isEmpty()) {
            $definition = new Collection(['type' => 'generic']);
        }

        return Field::factory($definition, $name);
    }

    public function getLabel(Field $field): string
    {
        return $field->getDefinition()->get('label');
    }

    public function getType(Field $field): string
    {
        return $field->getDefinition()->get('type');
    }
}
