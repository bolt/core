<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Handler;

use Bolt\Storage\Entity\Content;
use Bolt\Storage\Query\ContentQueryParser;
use Bolt\Storage\Query\QueryResultset;
use Bolt\Storage\Query\SelectQuery;
use Bolt\Storage\Repository;

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

            // $repo = $contentQuery->getEntityManager()->getRepository($contentType);
            // $query->setQueryBuilder($repo->createQueryBuilder('_' . $contentType));
            // $query->setContentType($contentType);

            /** Run the parameters through the whitelister. If we get a false back from this method it's because there
             * is no need to continue with the query.
             */
            // $params = $this->whitelistParameters($contentQuery->getParameters(), $repo);
            // if (!$params && count($contentQuery->getParameters())) {
            //     continue;
            // }
            //$params['contentType'] = $contentType;
            $params = $this->cleanParameters($contentQuery->getParameters());

            /* Continue and run the query add the results to the set */
            $query->setParameters($params);
            $contentQuery->runScopes($query);
            $contentQuery->runDirectives($query);
            // dd($query->build());
            // $query is of Bolt\Storage\Query\SelectQuery
            // dd($query->getQueryBuilder()->getQuery());

            $foo = $query->build();
            $foo->andWhere('content.contentType = :ct');
            $foo->setParameter('ct', $contentType);

            $this->setWhereParameters($foo, $qb, $repo, $contentQuery->getParameters()); // ROUGH!
            $result = $foo->getQuery()->getResult();

            if ($result) {
                $set->setOriginalQuery($contentType, $query->getQueryBuilder());
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

    // ---------------------------------------------------------------------------------------------

    var $coreFields = [
        'id',
        'created_at',
        'modified_at',
        'published_at',
        'depublished_at',
        'author_id',
        'status',
    ];

    /**
     *
     */
    private function cleanParameters($params)
    {
        $cleanParams = [];

        foreach ($params as $key => $value) {
            if (in_array($key, $this->coreFields)) {
                $cleanParams[$key] = $value;
            }
        }
        return $cleanParams;
    }

    /**
     *
     */
    private function setWhereParameters($q, $qb, $repo, $params)
    {
        // todo: Check if a key is a column in the `bolt_content` table or a row in the `bolt_fields` table.
        // todo: Is there a difference between taxonomies?
        // todo: Add a (default) locale check when it is ready.

        // Let's allow every other field, because it will not throw an error anymore. It
        // will just not return no results. In previous Bolt versions, it would throw an
        // exception if the column did not exist. We can still double check this with
        // `contenttypes.yml`.
        $index = 0;
        foreach ($params as $key => $value) {
            if (in_array($key, $this->coreFields)) {
              //something going on with id and slug blegh!
                // $q = $q
                //     ->andWhere('content.' . $key . ' = :key')
                //     ->setParmeter($key, $value)
                // ;
            } else {
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



    /**
     * This block is added to deal with the possibility that a requested filter is not an allowable option on the
     * database table. If the requested field filter is not a valid field on this table then we completely skip
     * the query because no results will be expected if the field does not exist. The exception to this is if the field
     * is part of an OR query then we remove the missing field from the stack but still allow the other fields through.
     *
     * @param array      $queryParams
     * @param Repository $repo
     *
     * @return bool|array $cleanParams
     */
    public function whitelistParameters(array $queryParams, $repo)
    {
        // $metadata = $repo->getClassMetadata();
        // $allowedParams = array_keys($metadata->getFieldMappings());
        $allowedParams = [
  0 => 'id',
  1 => 'slug',
  2 => 'datecreated',
  3 => 'datechanged',
  4 => 'datepublish',
  5 => 'datedepublish',
  6 => 'ownerid',
  7 => 'status',
  8 => 'templatefields',
  9 => 'title',
  10 => 'image',
  11 => 'teaser',
  12 => 'content',
  13 => 'contentlink',
  14 => 'incomingrelation',
        ];
        $cleanParams = [];
        foreach ($queryParams as $fieldSelect => $valueSelect) {
            $stack = [];

            if (is_string($valueSelect)) {
                $stack = preg_split('/ *(\|\|\|) */', $fieldSelect);
                $valueStack = preg_split('/ *(\|\|\|) */', $valueSelect);
            }

            if (count($stack) > 1) {
                $allowedKeys = [];
                $allowedVals = [];
                foreach ($stack as $i => $stackItem) {
                    if (in_array($stackItem, $allowedParams, true)) {
                        $allowedKeys[] = $stackItem;
                        $allowedVals[] = $valueStack[$i];
                    }
                }

                if (!count($allowedKeys)) {
                    return false;
                }
                $allowed = implode(' ||| ', $allowedKeys);
                $cleanParams[$allowed] = implode(' ||| ', $allowedVals);
            } else {
                if (!in_array($fieldSelect, $allowedParams, true)) {
                    return false;
                }
                $cleanParams[$fieldSelect] = $valueSelect;
            }
        }

        return $cleanParams;
    }
}
