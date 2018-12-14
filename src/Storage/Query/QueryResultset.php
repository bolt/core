<?php

declare(strict_types=1);

namespace Bolt\Storage\Query;

use AppendIterator;
use ArrayIterator;
use Countable;
use Doctrine\ORM\QueryBuilder;

/**
 * This class is a wrapper that handles single or multiple
 * sets or results fetched via a query. They can be iterated
 * normally, or split by label, eg just results from one
 * ContentType.
 */
class QueryResultset extends AppendIterator implements Countable
{
    /** @var array */
    protected $results = [];

    /** @var QueryBuilder[] */
    protected $originalQueries = [];

    /**
     * @param array  $results A set of results
     * @param string $type    An optional label to partition results
     */
    public function add($results, $type = null): void
    {
        if ($type) {
            $this->results[$type] = $results;
        } else {
            $this->results = array_merge($this->results, $results);
        }

        $this->append(new ArrayIterator($results));
    }

    /**
     * Allows retrieval of a set or results, if a label has been used to
     * store results then passing the label as a parameter returns just
     * that set of results.
     *
     * @param string $label
     */
    public function get($label = null): array
    {
        if ($label && array_key_exists($label, $this->results)) {
            return $this->results[$label];
        }
        $results = [];
        foreach ($this->results as $v) {
            if (is_array($v)) {
                $results = array_merge($results, $v);
            } else {
                $results[] = $v;
            }
        }

        return $results;
    }

    /**
     * Returns the total count.
     */
    public function count(): int
    {
        return count($this->get());
    }

    public function setOriginalQuery($type, $originalQuery): void
    {
        $this->originalQueries[$type] = $originalQuery;
    }

    public function getOriginalQuery($type = null): QueryBuilder
    {
        if ($type !== null) {
            return $this->originalQueries[$type];
        }

        return reset($this->originalQueries);
    }

    /**
     * @return QueryBuilder[]
     */
    public function getOriginalQueries(): array
    {
        return $this->originalQueries;
    }
}
