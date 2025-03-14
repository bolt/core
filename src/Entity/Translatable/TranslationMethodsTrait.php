<?php

declare(strict_types=1);

namespace Bolt\Entity\Translatable;

use Bolt\Entity\TranslatableInterface;

trait TranslationMethodsTrait
{
    public static function getTranslatableEntityClass(): string
    {
        // By default, the translatable class has the same name but without the "Translation" suffix
        return mb_substr(static::class, 0, -11, 'UTF-8');
    }

    /**
     * Sets entity, that this translation should be mapped to.
     */
    public function setTranslatable(TranslatableInterface $translatable): void
    {
        $this->translatable = $translatable;
    }

    /**
     * Returns entity, that this translation is mapped to.
     */
    public function getTranslatable(): TranslatableInterface
    {
        return $this->translatable;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function isEmpty(): bool
    {
        foreach (get_object_vars($this) as $var => $value) {
            if (in_array($var, ['id', 'translatable', 'locale'], true)) {
                continue;
            }

            if (is_string($value) && mb_strlen(trim($value)) > 0) {
                return false;
            }

            if (! empty($value)) {
                return false;
            }
        }

        return true;
    }
}
