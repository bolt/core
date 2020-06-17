<?php

declare(strict_types=1);

namespace Bolt\Storage\Directive;

use Bolt\Storage\QueryInterface;
use Bolt\Utils\ContentHelper;
use Symfony\Component\HttpFoundation\Request;

/**
 *  Directive to alter query based on 'order' parameter.
 *
 *  eg: 'pages', ['order'=>'-publishedAt']
 */
class OrderDirective
{
    public function __invoke(QueryInterface $query, string $order): void
    {
        if ($order === '') {
            return;
        }

        // todo: This should be passed somehow, not created on the fly.
        $locale = Request::createFromGlobals()->getLocale();

        // remove default order
        $query->getQueryBuilder()->resetDQLPart('orderBy');

        $separatedOrders = $this->getOrderBys($order);

        foreach ($separatedOrders as $order) {
            [ $order, $direction ] = $this->createSortBy($order);

            if ($order === 'title' && $this->getTitleFormat($query) !== null) {
                $order = ContentHelper::getFieldNames($this->getTitleFormat($query));
            }

            if (is_array($order)) {
                foreach ($order as $orderitem) {
                    $this->setOrderBy($query, $orderitem, $direction, $locale);
                }
            } else {
                $this->setOrderBy($query, $order, $direction, $locale);
            }
        }
    }

    /**
     * Set the query OrderBy directives
     * given an order (e.g. 'heading', 'id') and direction (ASC|DESC)
     */
    private function setOrderBy(QueryInterface $query, string $order, string $direction, string $locale): void
    {
        if (in_array($order, $query->getCoreFields(), true)) {
            $query->getQueryBuilder()->addOrderBy('content.' . $order, $direction);
        } elseif ($order === 'author') {
            $query
                ->getQueryBuilder()
                ->leftJoin('content.author', 'user')
                ->addOrderBy('user.username', $direction);
        } else {
            if (! $this->isActualField($query, $order)) {
                dump("A query with ordering on a Field (`${order}`) that's not defined, will yield unexpected results. Update your `{% setcontent %}`-statement");
            }
            $fieldsAlias = 'fields_order_' . $query->getIndex();
            $fieldAlias = 'order_' . $query->getIndex();
            $translationsAlias = 'translations_order_' . $query->getIndex();

            // Note the `lower()` in the `addOrderBy()`. It is essential to sorting the
            // results correctly. See also https://github.com/bolt/core/issues/1190
            $query
                ->getQueryBuilder()
                ->leftJoin('content.fields', $fieldsAlias)
                ->leftJoin($fieldsAlias . '.translations', $translationsAlias)
                ->andWhere($fieldsAlias . '.name = :' . $fieldAlias)
                ->andWhere($translationsAlias . '.locale = :locale')
                ->setParameter(':locale', $locale)
                ->addOrderBy('lower(' . $translationsAlias . '.value)', $direction)
                ->setParameter($fieldAlias, $order);

            $query->incrementIndex();
        }
    }

    /**
     * Cobble together the sorting order, and whether or not it's a column in `content` or `fields`.
     */
    private function createSortBy(string $order): array
    {
        if (mb_strpos($order, '-') === 0) {
            $direction = 'DESC';
            $order = mb_substr($order, 1);
        } elseif (mb_strpos($order, ' DESC') !== false) {
            $direction = 'DESC';
            $order = str_replace(' DESC', '', $order);
        } else {
            $order = str_replace(' ASC', '', $order);
            $direction = 'ASC';
        }

        return [$order, $direction];
    }

    protected function getOrderBys(string $order): array
    {
        $separatedOrders = [$order];

        if ($this->isMultiOrderQuery($order)) {
            $separatedOrders = explode(',', $order);
        }

        return $separatedOrders;
    }

    protected function isMultiOrderQuery(string $order): bool
    {
        return mb_strpos($order, ',') !== false;
    }

    protected function isActualField(QueryInterface $query, string $name): bool
    {
        $contentType = $query->getConfig()->get('contenttypes/' . $query->getContentType());

        return in_array($name, $contentType->get('fields')->keys()->all(), true);
    }

    private function getTitleFormat(QueryInterface $query): ?string
    {
        $contentType = $query->getConfig()->get('contenttypes/' . $query->getContentType());

        return $contentType->get('title_format', null);
    }
}
