<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Directive;

use Bolt\Storage\Query\SelectQuery;

/**
 *  Directive to add a limit modifier to the query.
 */
class OffsetDirective
{
    public function __invoke(SelectQuery $query, int $page, array $otherDirectives): void
    {
        $limit = $otherDirectives['limit'] ?: 0;
        $query->getQueryBuilder()->setFirstResult(($page - 1) * $limit);
    }
}
