<?php

declare(strict_types=1);

namespace Bolt\Enum;

class Statuses extends BaseEnum
{
    public const PUBLISHED = 'published';
    public const HELD = 'held';
    public const TIMED = 'timed';
    public const DRAFT = 'draft';
}
