<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Bolt\Configuration\Content\FieldType;

/**
 * Implements the methods of the FieldParentInterface.
 */
trait FieldParentTrait
{
    /** @var array Field */
    private $fields = [];

    abstract public function getContent(): ?Content;

    public function setLocale(?string $locale): Field
    {
        /** @var Field $child */
        foreach ($this->getValue() as $child) {
            if ($child instanceof Field) {
                $child->setLocale($locale);
            }
        }

        return $this;
    }

    /**
     * Override isTranslatable so that if one child definition
     * has localize: true, the whole field is considered localizable.
     */
    public function isTranslatable(): bool
    {
        /** @var FieldType $fieldDefinition */
        foreach ($this->getDefinition()->get('fields', []) as $fieldDefinition) {
            if ($fieldDefinition->get('localize', false)) {
                return true;
            }
        }

        return parent::isTranslatable();
    }
}
