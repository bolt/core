<?php

declare(strict_types=1);

namespace Bolt\Entity;

if (PHP_MAJOR_VERSION >= 8) {
    trait IterableFieldTrait
    {
        use IterableFieldTraitPhp8;
    }
} else {
    trait IterableFieldTrait
    {
        use IterableFieldTraitPhp7;
    }
}
