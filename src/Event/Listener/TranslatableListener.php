<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Entity\Translatable\BoltTranslatableInterface;
use Bolt\Entity\Translatable\BoltTranslationInterface;
use Bolt\Locale\LocaleProviderInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use ReflectionClass;

/**
 * @template Translatable of BoltTranslatableInterface
 * @template Translation of BoltTranslationInterface
 */
final readonly class TranslatableListener
{
    /**
     * @var string
     */
    public const LOCALE = 'locale';

    private int $translatableFetchMode;
    private int $translationFetchMode;

    public function __construct(
        private LocaleProviderInterface $localeProvider,
        string $translatableFetchMode,
        string $translationFetchMode
    ) {
        $this->translatableFetchMode = $this->convertFetchString($translatableFetchMode);
        $this->translationFetchMode = $this->convertFetchString($translationFetchMode);
    }

    /**
     * Adds mapping to the translatable and translations.
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $loadClassMetadataEventArgs): void
    {
        $classMetadata = $loadClassMetadataEventArgs->getClassMetadata();
        $reflectionClass = $classMetadata->reflClass;
        if (! $reflectionClass instanceof ReflectionClass) {
            // Class has not yet been fully built, ignore this event
            return;
        }

        if ($classMetadata->isMappedSuperclass) {
            return;
        }

        if (is_a($reflectionClass->getName(), BoltTranslatableInterface::class, true)) {
            /** @var ClassMetadata<Translatable> $classMetadata */
            $this->mapTranslatable($classMetadata);
        }

        if (is_a($reflectionClass->getName(), BoltTranslationInterface::class, true)) {
            /** @var ClassMetadata<Translation> $classMetadata */
            $this->mapTranslation($classMetadata, $loadClassMetadataEventArgs->getObjectManager());
        }
    }

    public function postLoad(PostLoadEventArgs $lifecycleEventArgs): void
    {
        $this->setLocales($lifecycleEventArgs->getObject());
    }

    public function prePersist(PrePersistEventArgs $lifecycleEventArgs): void
    {
        $this->setLocales($lifecycleEventArgs->getObject());
    }

    /**
     * Convert string FETCH mode to required string
     */
    private function convertFetchString(string|int $fetchMode): int
    {
        if (is_int($fetchMode)) {
            return $fetchMode;
        }

        if ($fetchMode === 'EAGER') {
            return ClassMetadata::FETCH_EAGER;
        }

        if ($fetchMode === 'EXTRA_LAZY') {
            return ClassMetadata::FETCH_EXTRA_LAZY;
        }

        return ClassMetadata::FETCH_LAZY;
    }

    /**
     * @param ClassMetadata<Translatable> $classMetadata
     */
    private function mapTranslatable(ClassMetadata $classMetadata): void
    {
        if ($classMetadata->hasAssociation('translations')) {
            return;
        }

        $classMetadata->mapOneToMany([
            'fieldName' => 'translations',
            'mappedBy' => 'translatable',
            'indexBy' => self::LOCALE,
            'cascade' => ['persist', 'remove'],
            'fetch' => $this->translatableFetchMode,
            'targetEntity' => $classMetadata->getReflectionClass()
                ->getMethod('getTranslationEntityClass')
                ->invoke(null),
            'orphanRemoval' => true,
        ]);
    }

    /**
     * @param ClassMetadata<Translation> $classMetadata
     */
    private function mapTranslation(ClassMetadata $classMetadata, ObjectManager $objectManager): void
    {
        if (! $classMetadata->hasAssociation('translatable')) {
            /** @var class-string<Translatable> $targetEntity */
            $targetEntity = $classMetadata->getReflectionClass()
                ->getMethod('getTranslatableEntityClass')
                ->invoke(null);

            /** @var ClassMetadata<Translatable> $targetClassMetadata */
            $targetClassMetadata = $objectManager->getClassMetadata($targetEntity);

            $singleIdentifierFieldName = $targetClassMetadata->getSingleIdentifierFieldName();

            $classMetadata->mapManyToOne([
                'fieldName' => 'translatable',
                'inversedBy' => 'translations',
                'cascade' => ['persist'],
                'fetch' => $this->translationFetchMode,
                'joinColumns' => [
                    [
                        'name' => 'translatable_id',
                        'referencedColumnName' => $singleIdentifierFieldName,
                        'onDelete' => 'CASCADE',
                    ],
                ],
                'targetEntity' => $targetEntity,
            ]);
        }

        $name = $classMetadata->getTableName() . '_unique_translation';
        if (! $this->hasUniqueTranslationConstraint($classMetadata, $name) &&
            $classMetadata->getName() === $classMetadata->rootEntityName) {
            $classMetadata->table['uniqueConstraints'][$name] = [
                'columns' => ['translatable_id', self::LOCALE],
            ];
        }

        if (! $classMetadata->hasField(self::LOCALE) && ! $classMetadata->hasAssociation(self::LOCALE)) {
            $classMetadata->mapField([
                'fieldName' => self::LOCALE,
                'type' => 'string',
                'length' => 5,
            ]);
        }
    }

    private function setLocales(object $entity): void
    {
        if (! $entity instanceof BoltTranslatableInterface) {
            return;
        }

        $currentLocale = $this->localeProvider->provideCurrentLocale();
        if ($currentLocale) {
            $entity->setCurrentLocale($currentLocale);
        }

        $fallbackLocale = $this->localeProvider->provideFallbackLocale();
        if ($fallbackLocale) {
            $entity->setDefaultLocale($fallbackLocale);
        }
    }

    /**
     * @param ClassMetadata<Translation> $classMetadata
     */
    private function hasUniqueTranslationConstraint(ClassMetadata $classMetadata, string $name): bool
    {
        return isset($classMetadata->table['uniqueConstraints'][$name]);
    }
}
