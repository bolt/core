<?php

declare(strict_types=1);

namespace Bolt\Entity;

trait IterableFieldTraitPhp8
{
    use IterableFieldTraitPhp7 {
        IterableFieldTraitPhp7::current as private parentCurrent;
    }

    /**
     * @return Field|string
     * @noinspection PhpMixedReturnTypeCanBeReducedInspection
     */
    public function current(): mixed
    {
        return $this->parentCurrent();
    }
}
