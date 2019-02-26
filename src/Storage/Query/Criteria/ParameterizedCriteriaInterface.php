<?php

namespace Bolt\Storage\Query\Criteria;

use Doctrine\Common\Collections\Criteria;

interface ParameterizedCriteriaInterface
{
    public function getCriteria(string $parameter, string $alias = 'f'): Criteria;
}
