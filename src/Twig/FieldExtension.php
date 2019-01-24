<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Field;
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

    public function fieldFactory(string $name, array $definition = []): Field
    {
        if (empty($definition)) {
            $definition = ['type' => 'generic'];
        }

        return Field::factory($definition, $name);
    }
}
