<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Directive;

use Bolt\Storage\Query\QueryInterface;

/**
 *  Directive to specify that a single object, rather than an array should be returned.
 */
class ReturnSingleDirective
{
    public function __invoke(QueryInterface $query): void
    {
        $query->getQueryBuilder()->setMaxResults(1);
        $query->setSingleFetchMode(true);
    }
}
