<?php

namespace Bolt\Storage\Query\Criteria;

use Bolt\Enum\Statuses;
use Doctrine\Common\Collections\Criteria;

class PublishedCriteria implements CriteriaInterface
{
    public function getCriteria(string $alias = 'c'): Criteria
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        return $criteria
            ->where(
                $expr->andX(
                    $expr->eq(sprintf('%s.published', $alias), Statuses::PUBLISHED)
                )
            );
    }
}
