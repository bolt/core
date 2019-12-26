<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ImagelistField extends Field implements FieldInterface
{
    public function getType(): string
    {
        return 'imagelist';
    }

    /**
     * Returns the value, as is in the database. Useful for processing, like
     * editing in the backend, where the results are to be serialised
     */
    public function getRawValue(): array
    {
        return (array) parent::getValue() ?: [];
    }

    /**
     * Returns the result, where the contained fields are "hydrated" as actual
     * Image Fields. For example, for iterating in the frontend.
     */
    public function getValue(): array
    {
        $result = [];

        foreach ($this->getRawValue() as $key => $image) {
            $imageField = new ImageField();
            $imageField->setName((string) $key);
            $imageField->setValue($image);
            array_push($result, $imageField);
        }

        return $result;
    }
}
