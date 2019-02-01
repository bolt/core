<?php

namespace Bolt\Storage\Query\Criteria;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;

class JoinCriteria extends Criteria
{
    /** @var array<Join> */
    private $joins = [];

    public function join(
        string $join,
        string $alias,
        ?string $conditionType = null,
        ?string $condition = null,
        ?string $indexBy = null
    ): self {
        return $this->innerJoin($join, $alias, $conditionType, $condition, $indexBy);
    }

    public function innerJoin(
        string $join,
        string $alias,
        ?string $conditionType = null,
        ?string $condition = null,
        ?string $indexBy = null
    ): self {
        $joinExpr = new Join(
            Join::INNER_JOIN, $join, $alias, $conditionType, $condition, $indexBy
        );
        $this->joins[$alias] = $joinExpr;

        return $this;
    }

    public function leftJoin(
        string $join,
        string $alias,
        ?string $conditionType = null,
        ?string $condition = null,
        ?string $indexBy = null
    ): self {
        $joinExpr = new Join(
            Join::LEFT_JOIN, $join, $alias, $conditionType, $condition, $indexBy
        );
        $this->joins[$alias] = $joinExpr;

        return $this;
    }

    public function getJoinConditions(): array
    {
        return $this->joins;
    }
}