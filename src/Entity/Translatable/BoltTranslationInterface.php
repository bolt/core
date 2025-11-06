<?php

declare(strict_types=1);

namespace Bolt\Entity\Translatable;

/** @template T of BoltTranslatableInterface */
interface BoltTranslationInterface
{
    /**
     * @return class-string<T>
     */
    public static function getTranslatableEntityClass(): string;

    /**
     * @param T $translatable
     */
    public function setTranslatable(BoltTranslatableInterface $translatable): void;

    /**
     * @return T|null
     */
    public function getTranslatable(): ?BoltTranslatableInterface;

    public function setLocale(string $locale): void;

    public function getLocale(): string;

    public function isEmpty(): bool;
}
