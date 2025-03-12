<?php

declare(strict_types=1);

namespace Bolt\Entity\Translatable;

use Bolt\Entity\TranslatableInterface;

trait TranslationPropertiesTrait
{
    /** @var string */
    protected $locale;

    /**
     * Will be mapped to translatable entity by TranslatableSubscriber
     *
     * @var TranslatableInterface
     */
    protected $translatable;
}
