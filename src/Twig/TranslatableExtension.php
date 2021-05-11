<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Field;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TranslatableExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('find_translations', [$this, 'findTranslations']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('translate', [$this, 'translate']),
        ];
    }

    public function findTranslations(TranslatableInterface $entity, ?string $locale = null): array
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
