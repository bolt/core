<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Doctrine\ORM\EntityManagerInterface;
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
            new TwigFilter('translated', [$this, 'findTranslated']),
        ];
    }


    public function findTranslations(TranslatableInterface $entity, ?string $locale = null): array
    {
        $translations = $entity->getTranslations();

        if ($locale) {
            return $translations[$locale] ?? null;
        }

        return $translations->toArray();
    }

    public function findTranslated(TranslatableInterface $entity, string $locale): TranslatableInterface
    {
        if ($locale === '') {
            // nothing to translate
            return $entity;
        }

        $entity->setLocale($locale);

        return $entity;
    }
}
