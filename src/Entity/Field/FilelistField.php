<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Configuration\Content\ContentType;
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

    public function getApiValue()
    {
        if (! $this->isTranslatable()) {
            return $this->getValue();
        }

        $result = [];

        foreach ($this->getTranslations() as $translation) {
            $locale = $translation->getLocale();
            $this->setCurrentLocale($locale);
            $value = $this->getValue();
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
