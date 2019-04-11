<?php

declare(strict_types=1);

namespace Bolt\Form;

use Bolt\Entity\Field;
use Symfony\Component\Form\DataTransformerInterface;

class FieldValueModelTransformer implements DataTransformerInterface
{
    /**
     * array => parsed value
     */
    public function transform($value)
    {
        return Field::parseValue($value);
    }

    /**
     * parsed value => array
     */
    public function reverseTransform($value)
    {
        return (array) $value;
    }
}
