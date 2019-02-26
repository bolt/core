<?php

namespace Bolt\Storage\Query\Criteria;

use Doctrine\Common\Collections\Criteria;

class ValueCriteria implements ParameterizedCriteriaInterface
{
    public function getCriteria(string $parameter, string $alias = 'f'): Criteria
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        return $criteria
            ->where(
                $expr->contains(sprintf('%s.value', $alias), $parameter)
            );
    }
}