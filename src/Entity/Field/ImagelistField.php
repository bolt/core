<?php

declare(strict_types=1);

namespace Bolt\Entity\Field;

use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Field;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\IterableFieldTrait;
use Bolt\Entity\ListFieldInterface;
use Doctrine\ORM\Mapping as ORM;
use Iterator;

/**
 * @ORM\Entity
 */
class ImagelistField extends Field implements FieldInterface, ListFieldInterface, RawPersistable, Iterator
{
    use IterableFieldTrait;

    public const TYPE = 'imagelist';

    /**
     * Returns the value, as is in the database. Useful for processing, like
     * editing in the backend, where the results are to be serialised
     */
    public function getRawValue(): array
    {
        $value = parent::getValue();

        return empty(current($value)) ? [] : (array) $value;
    }

    /**
     * Returns the value, where the contained fields are "hydrated" as actual
     * Image Fields. For example, for iterating in the frontend
     */
    public function getValue(): array
    {
        $this->fields = [];

        foreach ($this->getRawValue() as $key => $image) {
            $imageField = new ImageField();
            $imageField->setName((string) $key);
            $imageField->setValue($image);
            $this->fields[] = $imageField;
        }

        return $this->fields;
    }

    /**
     * @return mixed[]
     */
    public function getApiValue(): array
    {
        $result = [];

        if (! $this->isTranslatable()) {
            $images = $this->getValue();
            foreach ($images as $image) {
                $result[] = $image->getApiValue();
            }

            return $result;
        }

        foreach ($this->getTranslations() as $translation) {
            $locale = $translation->getLocale();
            $this->setCurrentLocale($locale);
            $images = $this->getValue();
            // $images is an array of ImageField.php
            // In the API we need the actual value of those fields, not the object
            $value = [];

            foreach ($images as $image) {
                $value[] = $image->getApiValue();
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
            /** @var ContentType $image */
            foreach (parent::getDefaultValue() as $key => $image) {
                $image = $image->toArray();
                $imageField = new ImageField();
                $imageField->setName((string) $key);
                $imageField->setValue($image);
                $result[] = $imageField;
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

        return json_encode(array_map(fn (ImageField $i): array => $i->getValue(), $values));
    }
}
