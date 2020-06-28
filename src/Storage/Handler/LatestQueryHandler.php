<?php

declare(strict_types=1);

namespace Bolt\Storage\Handler;

use Bolt\Storage\ContentQueryParser;
use Bolt\Storage\SelectQuery;
use Pagerfanta\Pagerfanta;

/**
 *  Handler to modify query based on activation of 'latest' modifier.
 *
 *  eg: 'pages/latest/10'
 */
class LatestQueryHandler
{
    public function __invoke(ContentQueryParser $contentQuery): Pagerfanta
    {
        $contentQuery->setDirective('order', '-id');

        // If we're using `/latest`, always return a paginator, even for Singletons
        $contentQuery->setDirective('returnsingle', false);

        return $contentQuery->getHandler('select')($contentQuery);
    }
}
