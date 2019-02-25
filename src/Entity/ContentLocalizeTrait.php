<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Tightenco\Collect\Support\Collection;

trait ContentLocalizeTrait
{
    public function getLocales(): Collection
    {
        $locales = $this->getDefinition()->get('locales');

        if ($locales->isEmpty()) {
            return new Collection(['']);
        }

        return $locales;
    }

    public function getDefaultLocale(): string
    {
        return $this->getLocales()->first();
    }
}
