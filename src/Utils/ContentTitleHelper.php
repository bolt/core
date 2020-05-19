<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Bolt\Entity\Content;
use Bolt\Entity\Field\Excerptable;

class ContentTitleHelper
{
    public static function isSuitable(Content $record, string $which = 'title_format'): bool
    {
        $definition = $record->getDefinition();

        if ($record->getId() && $definition !== null && $definition->has($which)) {
            $format = $definition->get($which);
            if (is_string($format) && mb_strpos($format, '{') !== false) {
                return true;
            }
        }

        return false;
    }

    public static function get(Content $record, string $format = '', string $locale = ''): string
    {
        if (empty($format)) {
            $format = '{title} (№ {id}, {status})';
        }

        if (empty($locale) && $record->hasContentTypeLocales()) {
            $locale = $record->getContentTypeDefaultLocale();
        }

        return preg_replace_callback(
            '/{([\w]+)}/i',
            function ($match) use ($record, $locale) {
                if ($match[1] === 'id') {
                    return $record->getId();
                }

                if ($match[1] === 'status') {
                    return $record->getStatus();
                }

                if ($record->hasField($match[1])) {
                    $field = $record->getField($match[1]);

                    if ($field->isTranslatable()) {
                        $field->setLocale($locale);
                    }

                    return $field;
                }

                if (array_key_exists($match[1], $record->getExtras())) {
                    return $record->getExtras()[$match[1]];
                }

                return '(unknown)';
            },
            $format
        );
    }

    public static function getFieldNames(string $format): array
    {
        preg_match_all('/{([\w]+)}/i', $format, $matches);

        return $matches[1];
    }

    public static function guessTitleFields(Content $content): array
    {
        // Check if we have a field named 'title' or somesuch.
        $names = ['title', 'name', 'caption', 'subject']; // English
        $names = array_merge($names, ['titel', 'naam', 'kop', 'onderwerp']); // Dutch
        $names = array_merge($names, ['nom', 'sujet']); // French
        $names = array_merge($names, ['nombre', 'sujeto']); // Spanish

        foreach ($names as $name) {
            if ($content->hasField($name)) {
                return (array) $name;
            }
        }

        foreach ($content->getFields() as $field) {
            if ($field instanceof Excerptable) {
                return (array) $field->getName();
            }
        }

        return [];
    }

    public static function getFieldBasedTitle(Content $content, string $locale = ''): string
    {
        $titleParts = [];

        foreach (self::guessTitleFields($content) as $fieldName) {
            $field = $content->getField($fieldName);

            if (! empty($locale)) {
                $field->setCurrentLocale($locale);
            }

            $value = $field->getParsedValue();

            if (empty($value)) {
                $value = $field->setLocale($field->getDefaultLocale())->getParsedValue();
            }

            $titleParts[] = $value;
        }

        return implode(' ', $titleParts);
    }
}
