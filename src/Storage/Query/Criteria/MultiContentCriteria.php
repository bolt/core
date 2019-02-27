<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Criteria;

use Doctrine\Common\Collections\Criteria;

class MultiContentCriteria implements ParameterizedCriteriaInterface
{
    public function getCriteria(string $parameter, string $alias = 'f'): Criteria
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();

        $contentTypes = explode(',', $parameter);

        return $criteria
            ->where(
                $expr->in(sprintf('%s.content_type', $alias), $contentTypes)
            );
    }
}
