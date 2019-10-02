<?php

declare(strict_types=1);

namespace Bolt\Storage\Handler;

use Bolt\Storage\ContentQueryParser;
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

        return $contentQuery->getHandler('select')($contentQuery);
    }
}
