<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Criteria;

use Doctrine\Common\Collections\Criteria;

interface CriteriaInterface
{
    public function getCriteria(string $alias = 'c'): Criteria;
}
