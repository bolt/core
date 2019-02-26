<?php

namespace Bolt\Storage\Query\Resolver;

use Tightenco\Collect\Support\Collection;

class CriteriaResolver
{
    public function resolve(array $parameters): Collection
    {
        $criteria = [];
        foreach ($parameters as $field => $value) {
            $criteria[] = $this->getCriteriaForField();
        }
    }
}