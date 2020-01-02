<?php

declare(strict_types=1);

namespace Bolt\Entity;

interface Translatable
{
    public function setLocale(string $locale): void;
    public function getLocale(): ?string;
}
