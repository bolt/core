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
    public function __invoke(ContentQueryParser $contentQuery): ?QueryResultset
    {
        $set = new QueryResultset();
        /** @var SelectQuery $query */
        $query = $contentQuery->getService('select');
        $query->setSingleFetchMode(false);

        foreach ($contentQuery->getContentTypes() as $contentType) {
            $contentType = str_replace('-', '_', $contentType);

            $repo = $contentQuery->getContentRepository();
            $qb = $repo->getQueryBuilder();
            $query->setQueryBuilder($qb);
            $query->setContentType('content');
            // $query->setAlias('content')

            $query->setParameters($contentQuery->getParameters());
            $contentQuery->runScopes($query);
            $contentQuery->runDirectives($query);

            // This is required. Not entirely sure why.
            $query->build();

            // Bolt4 introduces an extra table for field values, so additional
            // joins are required.
            $query->doReferenceJoins();
            $query->doFieldJoins();

            $query
                ->getQueryBuilder()
                ->andWhere('content.contentType = :ct')
                ->setParameter('ct', $contentType);

            $result = $query
                ->getQueryBuilder()
                ->getQuery()
                ->getResult();

            if ($result) {
                $set->setOriginalQuery($contentType, $query);
                $set->add($result, $contentType);
            }
        }

        if ($query->getSingleFetchMode()) {
            if ($set->count() === 0) {
                return null;
            }

            return $set->current();
        }

        return $set;
    }
}
