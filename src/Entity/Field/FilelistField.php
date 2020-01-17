<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FilelistField extends Field implements FieldInterface
{
    public const TYPE = 'filelist';

    /**
     * Returns the value, as is in the database. Useful for processing, like
     * editing in the backend, where the results are to be serialised
     */
    public function getRawValue(): array
    {
        return (array) parent::getValue() ?: [];
    }

    /**
     * Returns the value, where the contained fields are "hydrated" as actual
     * File Fields. For example, for iterating in the frontend
     */
    public function getValue(): array
    {
        $result = [];

        foreach ($this->getRawValue() as $key => $file) {
            $fileField = new FileField();
            $fileField->setName((string) $key);
            $fileField->setValue($file);
            array_push($result, $fileField);
        }

        return $result;
    }

    /**
     * Returns the value, where the contained Image fields are seperately
     * casted to arrays, including the "extras"
     */
    public function getJsonValue()
    {
        return json_encode(array_map(function (FileField $i) {
            return $i->getValue();
        }, $this->getValue()));
    }
}
