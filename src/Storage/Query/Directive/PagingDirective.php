<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Directive;

use Bolt\Storage\Query\QueryInterface;

/**
 *  Directive to add a limit modifier to the query.
 */
class PagingDirective
{
    /**
     * @param int $limit
     */
    public function __invoke(QueryInterface $query, $limit): void
    {
        // Not implemented yet
    }
}
