<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Directive;

use Bolt\Storage\Query\QueryInterface;

/**
 *  Directive to add a limit modifier to the query.
 */
class LimitDirective
{
    public function __invoke(QueryInterface $query, int $limit): void
    {
        $query->getQueryBuilder()->setMaxResults($limit);
    }
}
