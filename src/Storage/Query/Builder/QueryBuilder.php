<?php

namespace Bolt\Storage\Query\Builder;

use Bolt\Storage\Query\Criteria\JoinCriteria;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder as Builder;

class QueryBuilder extends Builder
{
    public function addCriteria(Criteria $criteria): Builder
    {
        $queryBuilder = parent::addCriteria($criteria);

        if ($criteria instanceof JoinCriteria) {
            /**
             * @var string $alias
             * @var Join $joinCondition
             */
            foreach($criteria->getJoinConditions() as $alias => $joinCondition) {
                $queryBuilder->add('join', [$alias, $joinCondition], true);
            }
        }

        return $this;
    }
}