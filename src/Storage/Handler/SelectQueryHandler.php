<?php

declare(strict_types=1);

namespace Bolt\Storage\Handler;

use Bolt\Entity\Content;
use Bolt\Storage\ContentQueryParser;
use Bolt\Storage\SelectQuery;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 *  Handler class to perform select query and return a resultset.
 */
class SelectQueryHandler
{
    /**
     * @return Content|Pagerfanta|null
     */
    public function __invoke(ContentQueryParser $contentQuery)
    {
        $repo = $contentQuery->getContentRepository();
        $qb = $repo->getQueryBuilder();

        /** @var SelectQuery $selectQuery */
        $selectQuery = $contentQuery->getService('select');
        $selectQuery->setSingleFetchMode(false);

        $selectQuery->setQueryBuilder($qb);
        $selectQuery->setContentTypeFilter($contentQuery->getContentTypes());
        $selectQuery->setParameters($contentQuery->getParameters());

        $contentQuery->runScopes($selectQuery);
        $contentQuery->runDirectives($selectQuery);

        // This is required. Not entirely sure why.
        $selectQuery->build();

        // Bolt4 introduces an extra table for field values, so additional
        // joins are required.
        $selectQuery->doReferenceJoins();
        $selectQuery->doFieldJoins();

        if ($selectQuery->getSingleFetchMode()) {
            return $qb
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }

        $query = $qb->getQuery();

        return $this->createPaginator($query, 1, 4);
    }

    private function createPaginator(Query $query, int $page, int $amountPerPage): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query, true, true));
        $paginator->setMaxPerPage($amountPerPage);
        $paginator->setCurrentPage($page);
        return $paginator;
    }
}
