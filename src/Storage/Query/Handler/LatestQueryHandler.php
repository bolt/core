<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Handler;

use Bolt\Storage\Query\ContentQueryParser;
use Bolt\Storage\Query\QueryResultset;

/**
 *  Handler to modify query based on activation of 'latest' modifier.
 *
 *  eg: 'pages/latest/10'
 */
class LatestQueryHandler
{
    public function __invoke(ContentQueryParser $contentQuery): QueryResultset
    {
        $contentQuery->setDirective('order', '-id');

        return $contentQuery->getHandler('select')($contentQuery);
    }
}
