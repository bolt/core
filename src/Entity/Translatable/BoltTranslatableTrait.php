<?php

declare(strict_types=1);

namespace Bolt\Entity\Translatable;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Should be used inside entity that needs to be translated.
 *
 * @template-covariant T of BoltTranslationInterface
 */
trait BoltTranslatableTrait
{
    /**
     * @return Collection<string, T>
     */
    public function getTranslations(): Collection
    {
        // initialize collection, usually in ctor
        if ($this->translations === null) {
            $this->translations = new ArrayCollection();
        }

        return $this->translations;
    }

    /**
     * @param iterable<T> $translations
     */
    public function setTranslations(iterable $translations): void
    {
        foreach ($translations as $translation) {
            $this->addTranslation($translation);
        }
    }

    /**
     * @return Collection<string, T>
     */
    public function getNewTranslations(): Collection
    {
        // initialize collection, usually in ctor
        if ($this->newTranslations === null) {
            $this->newTranslations = new ArrayCollection();
        }

        return $this->newTranslations;
    }

    /**
     * @param T $translation
     */
    public function addTranslation(BoltTranslationInterface $translation): void
    {
        $this->getTranslations()
            ->set($translation->getLocale(), $translation);
        $translation->setTranslatable($this);
    }

    /**
     * @param T $translation
     */
    public function removeTranslation(BoltTranslationInterface $translation): void
    {
        $this->getTranslations()
            ->removeElement($translation);
    }

    /**
     * Returns translation for specific locale (creates new one if doesn't exists). If requested translation doesn't
     * exist, it will first try to fallback default locale If any translation doesn't exist, it will be added to
     * newTranslations collection. In order to persist new translations, call mergeNewTranslations method, before flush
     *
     * @param string|null $locale The locale (en, ru, fr) | null If null, will try with current locale
     *
     * @return T
     */
    public function translate(?string $locale = null, bool $fallbackToDefault = true): BoltTranslationInterface
    {
        return $this->doTranslate($locale, $fallbackToDefault);
    }

    /**
     * Merges newly created translations into persisted translations.
     */
    public function mergeNewTranslations(): void
    {
        foreach ($this->getNewTranslations() as $newTranslation) {
            if (! $this->getTranslations()->contains($newTranslation) && ! $newTranslation->isEmpty()) {
                $this->addTranslation($newTranslation);
                $this->getNewTranslations()
                    ->removeElement($newTranslation);
            }
        }

        foreach ($this->getTranslations() as $translation) {
            if (! $translation->isEmpty()) {
                continue;
            }

            $this->removeTranslation($translation);
        }
    }

    public function setCurrentLocale(string $locale): void
    {
        $this->currentLocale = $locale;
    }

    public function setDefaultLocale(string $locale): void
    {
        $this->defaultLocale = $locale;
    }

    /**
     * Returns translation for specific locale (creates new one if doesn't exists). If requested translation doesn't
     * exist, it will first try to fallback default locale If any translation doesn't exist, it will be added to
     * newTranslations collection. In order to persist new translations, call mergeNewTranslations method, before flush
     *
     * @param string|null $locale The locale (en, ru, fr) | null If null, will try with current locale
     *
     * @return T
     */
    protected function doTranslate(?string $locale = null, bool $fallbackToDefault = true): BoltTranslationInterface
    {
        if ($locale === null) {
            $locale = $this->getCurrentLocale();
        }

        $foundTranslation = $this->findTranslationByLocale($locale);
        if ($foundTranslation && ! $foundTranslation->isEmpty()) {
            return $foundTranslation;
        }

        if ($fallbackToDefault) {
            $fallbackTranslation = $this->resolveFallbackTranslation($locale);
            if ($fallbackTranslation !== null) {
                return $fallbackTranslation;
            }
        }

        if ($foundTranslation) {
            return $foundTranslation;
        }

        $translationEntityClass = static::getTranslationEntityClass();

        /** @var T $translation */
        $translation = new $translationEntityClass();
        $translation->setLocale($locale);

        $this->getNewTranslations()
            ->set($translation->getLocale(), $translation);
        $translation->setTranslatable($this);

        return $translation;
    }

    /**
     * An extra feature allows you to proxy translated fields of a translatable entity.
     *
     * @param array<int|string, mixed> $arguments
     *
     * @return mixed The translated value of the field for current locale
     */
    protected function proxyCurrentLocaleTranslation(string $method, array $arguments = []): mixed
    {
        // allow $entity->name call $entity->getName() in templates
        if (! method_exists(self::getTranslationEntityClass(), $method)) {
            $method = 'get' . ucfirst($method);
        }

        $translation = $this->translate($this->getCurrentLocale());

        return call_user_func_array($translation->{$method}(...), $arguments);
    }

    /**
     * Finds specific translation in collection by its locale.
     *
     * @return T|null
     */
    protected function findTranslationByLocale(string $locale, bool $withNewTranslations = true): ?BoltTranslationInterface
    {
        $translation = $this->getTranslations()
            ->get($locale);

        if ($translation) {
            return $translation;
        }

        if ($withNewTranslations) {
            return $this->getNewTranslations()
                ->get($locale);
        }

        return null;
    }

    protected function computeFallbackLocale(string $locale): ?string
    {
        if (mb_strrchr($locale, '_') !== false) {
            return mb_substr($locale, 0, -mb_strlen(mb_strrchr($locale, '_')));
        }

        return null;
    }

    /**
     * @return T|null
     */
    private function resolveFallbackTranslation(string $locale): ?BoltTranslationInterface
    {
        $fallbackLocale = $this->computeFallbackLocale($locale);

        if ($fallbackLocale !== null) {
            $translation = $this->findTranslationByLocale($fallbackLocale);
            if ($translation && ! $translation->isEmpty()) {
                return $translation;
            }
        }

        return $this->findTranslationByLocale($this->getDefaultLocale(), false);
    }

    /**
     * @return class-string<T>
     */
    public static function getTranslationEntityClass(): string
    {
        return self::class . 'Translation';
    }

    /**
     * Will be mapped to translatable entity by TranslatableListener.
     *
     * The Type attribute (translation) is used by our serializer subscriber (idb.idb.jms.serialization.subscriber)
     * to identify this field type and to place the correct Translation type in the serializer.
     * Because we use both JSON and XML, we also need to provide the XmlMap property. Without this map property,
     * deserialization of the object fails because of missing metadata.
     *
     * @var Collection<string, T>|null
     */
    protected ?Collection $translations = null;

    /**
     * Will be merged with persisted translations on mergeNewTranslations call.
     *
     * @see mergeNewTranslations
     *
     * @var Collection<string, T>|null
     */
    protected ?Collection $newTranslations = null;

    /**
     * Default locale as fallback when it is not available in the request.
     * It is a non persisted field set during postLoad event.
     */
    protected ?string $currentLocale = null;

    public function getCurrentLocale(): string
    {
        return $this->currentLocale ?: $this->getDefaultLocale();
    }

    protected string $defaultLocale = 'en';

    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }
}
