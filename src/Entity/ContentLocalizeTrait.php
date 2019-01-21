<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Tightenco\Collect\Support\Collection;

trait ContentLocalizeTrait
{
    public function getLocales(): Collection
    {
        $locales = $this->getDefinition()->get('locales');

        if (empty($locales)) {
            $locales = [''];
        }

        return collect($locales);
    }

    public function getDefaultLocale(): string
    {
        return $this->getLocales()->first();
    }
//    public function getLocalizedFields(string $locale = '', bool $fallback = true): Collection
//    {
//        if (! $locale) {
//            $locale = $this->getDefaultLocale();
//        }
//
//        $fields = [];
//
//        foreach ($this->getDefinition()->get('fields') as $name => $field) {
//            $field = $this->getLocalizedField($name, $locale, $fallback, $field);
//            $fields[$name] = $field;
//        }
//
//        return $fields;
//    }
//
//    public function hasLocalizedField(string $fieldName, string $locale): bool
//    {
//        if ($this->hasField($fieldName) === false) {
//            return false;
//        }
//
//        $field = $this->getField($fieldName);
//
//        return $field->getDefinition()->get('localize') === false || $field->getLocale() === $locale;
//    }
//
//    public function getLocalizedField(string $name, string $locale, bool $fallback = true, array $definition = [])
//    {
//        // First: see if we have the field, in the correct locale
//        if ($this->hasLocalizedField($name, $locale)) {
//            return $this->getField($name);
//        }
//
//        // Second: see if we have the field, in the fallback locale
//        if ($fallback) {
//            foreach ($this->fields as $field) {
//                if ($field->getName() === $name && $field->getLocale() === $this->getDefaultLocale()) {
//                    return $field;
//                }
//            }
//        }
//
//        // Third: see if we have the field, with no locale set, or we're asking for the default locale
//        if ($fallback || $locale === $this->getDefaultLocale()) {
//            foreach ($this->fields as $field) {
//                if ($field->getName() === $name && empty($field->getLocale())) {
//                    return $field;
//                }
//            }
//        }
//
//        // Fourth: see if we can create the field on the fly
//        if (! empty($definition)) {
//            return Field::factory($definition, $name);
//        }
//
//        // Alas, return an empty generic Field
//        return Field::factory(['type' => 'generic']);
//    }
}
