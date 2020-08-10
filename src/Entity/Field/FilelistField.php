<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\ListFieldInterface;
use Bolt\Entity\ListFieldTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class FilelistField extends Field implements FieldInterface, ListFieldInterface, RawPersistable, \Iterator
{
    use ListFieldTrait;

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
        $this->fields = [];

        foreach ($this->getRawValue() as $key => $file) {
            $fileField = new FileField();
            $fileField->setName((string) $key);
            $fileField->setValue($file);
            $this->fields[] = $fileField;
        }

        return $this->fields;
    }

    public function getApiValue()
    {
        $result = [];

        if (! $this->isTranslatable()) {
            $files = $this->getValue();
            foreach ($files as $file) {
                $result[] = $file->getApiValue();
            }

            return $result;
        }

        foreach ($this->getTranslations() as $translation) {
            $locale = $translation->getLocale();
            $this->setCurrentLocale($locale);
            $files = $this->getValue();
            // $files is an array of FileField.php
            // In the API we need the actual value of those fields, not the object
            $value = [];

            foreach ($files as $file) {
                $value[] = $file->getApiValue();
            }

            $result[$locale] = $value;
        }

        return $result;
    }

    public function getDefaultValue(): array
    {
        $result = [];

        $values = parent::getDefaultValue();

        if ($values !== null) {
            /** @var ContentType $file */
            foreach (parent::getDefaultValue() as $key => $file) {
                $file = $file->toArray();
                $fileField = new FileField();
                $fileField->setName((string) $key);
                $fileField->setValue($file);
                $result[] = $fileField;
            }
        }

        return $result;
    }

    /**
     * Returns the value, where the contained Image fields are seperately
     * casted to arrays, including the "extras"
     */
    public function getJsonValue()
    {
        if ($this->isNew()) {
            $values = $this->getDefaultValue();
        } else {
            $values = $this->getValue();
        }

        return json_encode(array_map(function (FileField $i) {
            return $i->getValue();
        }, $values));
    }
}
