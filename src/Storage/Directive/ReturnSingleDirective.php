<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Bolt\Storage\QueryInterface;
use Bolt\Storage\SelectQuery;

/**
 *  Directive to specify that a single object, rather than an array should be returned.
 */
class ReturnSingleDirective
{
    public function __invoke(QueryInterface $query): void
    {
        $query->getQueryBuilder()->setMaxResults(1);
        if ($query instanceof SelectQuery) {
            $query->setSingleFetchMode(true);
        }
    }
}
