<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Bolt\Storage\SelectQuery;

/**
 *  Directive to specify that an array, rather than a single object should be returned.
 */
class ReturnMultipleDirective
{
    public const NAME = 'returnmultiple';

    public function __invoke(SelectQuery $query): void
    {
        $query->setSingleFetchMode(false);
    }
}
