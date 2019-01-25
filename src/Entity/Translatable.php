<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Gedmo\Translatable\Translatable as GedmoTranslatable;

interface Translatable extends GedmoTranslatable
{
    public function setLocale(string $locale): void;
    public function getLocale(): ?string;
}
