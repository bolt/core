<?php

declare(strict_types=1);

namespace Bolt\Storage\Query\Directive;

use Bolt\Storage\Query\QueryInterface;

/**
 *  Directive to alter query based on 'order' parameter.
 *
 *  eg: 'pages', ['order'=>'-publishedAt']
 */
class OrderDirective
{
    /**
     * @param QueryInterface $query
     * @param string         $order
     */
    public function __invoke(QueryInterface $query, $order)
    {
        if (!$order) {
            return;
        }

        // remove default order
        $query->getQueryBuilder()->resetDQLPart('orderBy');

        $separatedOrders = $this->getOrderBys($order);
        foreach ($separatedOrders as $order) {
            $order = trim($order);
            if (mb_strpos($order, '-') === 0) {
                $direction = 'DESC';
                $order = mb_substr($order, 1);
            } elseif (mb_strpos($order, ' DESC') !== false) {
                $direction = 'DESC';
                $order = str_replace(' DESC', '', $order);
            } else {
                $direction = null;
            }
            $query->getQueryBuilder()->addOrderBy('content.' . $order, $direction);
        }
    }

    /**
     * @param $order
     *
     * @return array
     */
    protected function getOrderBys($order)
    {
        $separatedOrders = [$order];

        if ($this->isMultiOrderQuery($order)) {
            $separatedOrders = explode(',', $order);
        }

        return $separatedOrders;
    }

    /**
     * @param $order
     *
     * @return bool
     */
    protected function isMultiOrderQuery($order)
    {
        return mb_strpos($order, ',') !== false;
    }
}
