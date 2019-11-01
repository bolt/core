<?php

declare(strict_types=1);

namespace Bolt\Entity;

/**
 * Any Field entity must implement this interface to be properly mapped.
 */
interface FieldInterface
{
    public function getType(): string;

    public function isContentSelect(): bool;
}
