<?php

declare(strict_types=1);

namespace Bolt\Extension;

/**
 * BaseWidget can be used as easy starter pack or as a base for your own extensions.
 */
abstract class BaseExtension implements ExtensionInterface
{
    public function getName(): string
    {
        return 'BaseExtension';
    }

    public function getClass(): string
    {
        return static::class;
    }

    public function initialize(): void
    {
        // Nothing
    }
}
