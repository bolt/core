<?php


namespace Bolt\Storage\Query\Criteria;


use Doctrine\Common\Collections\Criteria;

class ContentCriteria implements ParameterizedCriteriaInterface
{
    public function getCriteria(string $parameter, string $alias = 'f'): Criteria
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        return $criteria
            ->where(
                $expr->eq(sprintf('%s.content_type', $alias), $parameter)
            );
    }
}