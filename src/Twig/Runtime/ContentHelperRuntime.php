<?php

declare(strict_types=1);

namespace Bolt\Twig\Runtime;

use Bolt\Entity\Field;

class ContentHelperRuntime
{
    public function dummy($input = null)
    {
        return $input;
    }

    public function selectoptionsfromarray(Field $field)
    {
        $values = $field->getDefinition()->get('values');
        $currentValues = $field->getValue();

        $options = [];

        if ($field->getDefinition()->get('required', false)) {
            $options[] = [
                'key' => '',
                'value' => '',
                'selected' => false,
            ];
        }

        foreach ($values as $key => $value) {
            $options[] = [
                'key' => $key,
                'value' => $value,
                'selected' => in_array($key, $currentValues, true),
            ];
        }

        return $options;
    }
}
