<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Field;
use Illuminate\Support\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FieldExtension extends AbstractExtension
{
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
}
