<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Handler;

use Bolt\Entity\Content;
use Bolt\Storage\Query\ContentQueryParser;
use Bolt\Storage\Query\QueryResultset;
use Bolt\Storage\Query\SelectQuery;

/**
 *  Handler class to perform select query and return a resultset.
 */
class SelectQueryHandler
{
    /**
     * @return Content|QueryResultSet|null
     */
    public function __invoke(ContentQueryParser $contentQuery)
    {
        $set = new QueryResultset();
        /** @var SelectQuery $selectQuery */
        $selectQuery = $contentQuery->getService('select');
        $selectQuery->setSingleFetchMode(false);

        foreach ($contentQuery->getContentTypes() as $contentType) {
            $contentType = str_replace('-', '_', $contentType);

            $repo = $contentQuery->getContentRepository();
            $qb = $repo->getQueryBuilder();
            $selectQuery->setQueryBuilder($qb);
            $selectQuery->setContentType('content');
            // $query->setAlias('content')

            $selectQuery->setParameters($contentQuery->getParameters());
            $contentQuery->runScopes($selectQuery);
            $contentQuery->runDirectives($selectQuery);

            // This is required. Not entirely sure why.
            $selectQuery->build();

            // Bolt4 introduces an extra table for field values, so additional
            // joins are required.
            $selectQuery->doReferenceJoins();
            $selectQuery->doFieldJoins();

            $selectQuery
                ->getQueryBuilder()
                ->andWhere('content.contentType = :ct')
                ->setParameter('ct', $contentType);

            $query = $selectQuery
                ->getQueryBuilder()
                ->getQuery();

            $set->setOriginalQuery($contentType, $query);

            $result = $query
                ->getResult();

            if ($result) {
                $set->add($result, $contentType);
            }
        }

        if ($selectQuery->getSingleFetchMode()) {
            if ($set->count() === 0) {
                return null;
            }

            return $set->current();
        }

        return $set;
    }
}
