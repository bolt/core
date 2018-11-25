<?php

declare(strict_types=1);

namespace Bolt\Entity;

trait ContentLocaliseTraits
{
    public function getLocales()
    {
        $locales = $this->getDefinition()->get('locales');

        if (empty($locales)) {
            $locales = [''];
        }

        return collect($locales);
    }

    public function getDefaultLocale()
    {
        return $this->getLocales()->first();
    }

    public function getLocalisedFields(string $locale = '', bool $fallback = true): \Tightenco\Collect\Support\Collection
    {
        if (! $locale) {
            $locale = $this->getDefaultLocale();
        }

        $fields = collect([]);

        foreach ($this->getDefinition()->get('fields') as $name => $field) {
            $field = $this->getLocalisedField($name, $locale, $fallback, $field);
            $fields->put($name, $field);
        }

        return $fields;
    }

    public function hasLocalisedField(string $name, string $locale = '')
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name && in_array($field->getLocale(), [$locale, '', null], true)) {
                return true;
            }
        }

        return false;
    }

    public function getLocalisedField(string $name, string $locale = '', bool $fallback = true, array $definition = [])
    {
        // First, see if we have the field, in the correct locale
        foreach ($this->fields as $field) {
            if ($field->getName() === $name && in_array($field->getLocale(), [$locale, '', null], true)) {
                return $field;
            }
        }

        // Second, see if we have the field, in the fallback locale
        if ($fallback) {
            foreach ($this->fields as $field) {
                if ($field->getName() === $name && $field->getLocale() === $this->getDefaultLocale()) {
                    return $field;
                }
            }
        }

        // Third, see if we can create the field on the fly
        if (! empty($definition)) {
            return Field::factory($definition, $name);
        }

        // Alas, return an empty generic Field
        return Field::factory(['type' => 'generic']);
    }
}
