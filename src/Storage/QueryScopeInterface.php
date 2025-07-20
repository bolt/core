<?php

declare(strict_types=1);

namespace Bolt\Storage;

/**
 * Interface defines a class that provides additional scoping for a Content Query.
 *
 * @author Ross Riley <riley.ross@gmail.com>
 */
interface QueryScopeInterface
{
    public function onQueryExecute(QueryInterface $query);
}
