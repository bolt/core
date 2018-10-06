<?php

declare(strict_types=1);

namespace Bolt\Content;

use Tightenco\Collect\Support\Collection;

final class FieldType extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct(array_merge($this->defaults(), $items));
    }

    private function defaults()
    {
        $values = [
            'type' => '',
            'class' => '',
            'group' => '',
            'label' => '',
            'variant' => '',
            'postfix' => '',
            'prefix' => '',
            'placeholder' => '',
            'sort' => '',
            'default' => ''
        ];

        return $values;
    }
}
