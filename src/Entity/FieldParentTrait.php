<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Tightenco\Collect\Support\Collection;

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
     * has localize: true, the whole field is considered localized.
     */
    public function isTranslatable(): bool
    {
        return $this->shouldThisBeTranslatable($this->getDefinition());
    }

    private function shouldThisBeTranslatable(Collection $definition): bool
    {
        if ($definition->has('fields')) {
            foreach ($definition->get('fields') as $fieldDefinition) {
                $result = $this->shouldThisBeTranslatable($fieldDefinition);
                if ($result) {
                    return true;
                }
            }
        }

        // todo: This is a duplication of Field::isTranslatable()
        // but if it's in Field, it requires the thing to be a field...
        return $definition->get('localize');
    }
}
