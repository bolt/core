<?php

declare(strict_types=1);

namespace Bolt\Entity;

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
}
