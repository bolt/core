<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Bolt\Storage\SelectQuery;

/**
 *  Directive to specify that a single object, rather than an array should be returned.
 */
class ReturnSingleDirective
{
    public function __invoke(SelectQuery $query): void
    {
        $query->setSingleFetchMode((bool) $query->getParameter('returnsingle'));
    }
}
