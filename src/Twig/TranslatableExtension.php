<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Translatable;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Translatable\Entity\Repository\TranslationRepository;
use Gedmo\Translatable\Entity\Translation;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TranslatableExtension extends AbstractExtension
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TranslationRepository
     */
    private $translationRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        /** @var TranslationRepository $translationRepository */
        $translationRepository = $em->getRepository(Translation::class);
        $this->translationRepository = $translationRepository;
    }

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

    public function findTranslations(Translatable $entity, ?string $locale = null)
    {
        $translations = $this->translationRepository->findTranslations($entity);
        if ($locale) {
            return $translations[$locale] ?? null;
        }

        return $translations;
    }

    public function findTranslated(Translatable $entity, string $locale): Translatable
    {
        if ($locale === '') {
            // nothing to translate
            return $entity;
        }

        $entity->setLocale($locale);
        $this->em->refresh($entity);

        return $entity;
    }
}
