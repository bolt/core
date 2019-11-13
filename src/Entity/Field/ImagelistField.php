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

    public function getValue(): array
    {
        $images = (array) parent::getValue() ?: [];

        $result = [];

        foreach ($images as $key => $image) {
            $imageField = new ImageField();
            $imageField->setName((string) $key);
            $imageField->setValue($image);
            array_push($result, $imageField->getValue());
        }

        return $result;
    }
}
