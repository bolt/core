<?php

declare(strict_types=1);

namespace Bolt\Locale;

interface LocaleProviderInterface
{
    public function provideCurrentLocale(): ?string;

    public function provideFallbackLocale(): ?string;
}
