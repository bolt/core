<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Handler;

use Bolt\Storage\Entity\Content;
use Bolt\Storage\Query\ContentQueryParser;
use Bolt\Storage\Query\QueryResultset;
use Bolt\Storage\Query\SelectQuery;
use Bolt\Storage\Repository;
use Doctrine\ORM\QueryBuilder;

/**
 *  Handler class to perform select query and return a resultset.
 */
class SelectQueryHandler
{
    /**
     * @param ContentQueryParser $contentQuery
     *
     * @return QueryResultset|Content|false
     */
    public function __invoke(ContentQueryParser $contentQuery)
    {
        $set = new QueryResultset();
        /** @var SelectQuery $query */
        $query = $contentQuery->getService('select');
        $query->setSingleFetchMode(false);

        foreach ($contentQuery->getContentTypes() as $contentType) {
            $contentType = str_replace('-', '_', $contentType);

            $repo = $contentQuery->getContentRepository();
            $qb   = $repo->getQueryBuilder();
            $query->setQueryBuilder($qb);
            // $query->setContentType($contentType);
            $query->setContentType('content');

            $params = $this->cleanParameters($contentQuery->getParameters());

            /* Continue and run the query add the results to the set */
            $query->setParameters($params);
            $contentQuery->runScopes($query);
            $contentQuery->runDirectives($query);

            $query
                ->getQueryBuilder()
                ->andWhere('content.contentType = :ct')
                ->setParameter('ct', $contentType)
            ;

            // $this->setWhereParameters($query->getQueryBuilder(), $qb, $repo, $contentQuery->getParameters());
            $this->setWhereParameters($query->getQueryBuilder(), $contentQuery);

            $result = $query->getQueryBuilder()->getQuery()->getResult();

            if ($result) {
                $set->setOriginalQuery($contentType, $query);
                $set->add($result, $contentType);
            }
        }

        if ($query->getSingleFetchMode()) {
            if ($set->count() === 0) {
                return false;
            }

            return $set->current();
        }

        return $set;
    }

    private $coreFields = [
        'id',
        'createdAt',
        'modifiedAt',
        'publishedAt',
        'depublishedAt',
        // 'authorId', // references are to be handled differently!
        'status',
    ];

    private function cleanParameters(array $params): array
    {
        $cleanParams = [];

        foreach ($params as $key => $value) {
            if (in_array($key, $this->coreFields)) {
                $cleanParams[$key] = $value;
            }
        }

        return $cleanParams;
    }

    // `QueryParameterParser`
    private function setWhereParameters(QueryBuilder $q, ContentQueryParser $contentQuery)
    {
        // todo: Check if a key is a column in the `bolt_content` table or a row in the `bolt_fields` table.
        // todo: Add a (default) locale check when it is ready.

        $repo   = $contentQuery->getContentRepository();
        $qb     = $repo->getQueryBuilder();
        $params = $contentQuery->getParameters();

        $index = 0;
        foreach ($params as $key => $value) {
            if (false) {
                // todo: handle `|||` queries
            } elseif (in_array($key, $this->coreFields)) {
                // handled in `QueryParameterParser`
            } elseif ($key === 'author') {
                // todo: Move to `QueryParameterParser`
                $q = $q
                    ->join('content.author', 'a')
                    ->andWhere('a.id = :author')
                    ->setParameter('author', $value)
                ;
            } else {
                // todo: Move to `QueryParameterParser`
                $contentAlias = 'content_' . $index;
                $fieldsAlias  = 'fields_' . $index;
                $keyParam     = 'field_' . $index;
                $valueParam   = 'value_' . $index;

                $q = $q
                    ->andWhere(
                        $qb->expr()->in('content.id',
                          $repo
                            ->createQueryBuilder($contentAlias)
                            ->select($contentAlias . '.id')
                            ->innerJoin($contentAlias. '.fields', $fieldsAlias)
                            ->andWhere($fieldsAlias . '.name = :' . $keyParam)
                            ->andWhere($fieldsAlias . '.value = :' . $valueParam)
                            ->getDQL()
                        )
                    )
                    ->setParameter($keyParam, $key)
                    ->setParameter($valueParam, \GuzzleHttp\json_encode([ $value ]))
                ;

                $index++;
            }
        }

        return $q;
    }
}
