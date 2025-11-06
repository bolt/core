<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Field;
use Bolt\Entity\Translatable\BoltTranslatableInterface;
use Bolt\Entity\Translatable\BoltTranslationInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TranslatableExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('find_translations', $this->findTranslations(...)),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('translate', $this->translate(...)),
        ];
    }

    /**
     * @template T of BoltTranslatableInterface
     * @param T $entity
     * @return BoltTranslationInterface<T>|BoltTranslationInterface<T>[]|null
     */
    public function findTranslations(BoltTranslatableInterface $entity, ?string $locale = null): BoltTranslationInterface|array|null
    {
        $translations = $entity->getTranslations()->toArray();

        if ($locale) {
            return $translations[$locale] ?? null;
        }

        return $translations;
    }

    public function translate(Field $entity, string $locale): Field
    {
        if ($locale === '') {
            // nothing to translate
            return $entity;
        }

        $entity->setLocale($locale);

        return $entity;
    }
}
