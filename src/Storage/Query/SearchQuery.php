<?php

declare(strict_types=1);

namespace Bolt\Storage\Query;

use Bolt\Exception\QueryParseException;
use Doctrine\ORM\Query\Expr\Composite;
use Doctrine\ORM\QueryBuilder;

/**
 * This query class coordinates a search query building mainly on the same
 * filtering system used in the SelectQuery class. The main difference is
 * the addition of weighting, which is driven by documented here:.
 *
 *  @see https://docs.bolt.cm/templates/content-search
 *
 *  The resulting QueryBuilder object is then passed through to the individual
 *  field handlers where they can perform value transformations.
 *
 *  @author Ross Riley <riley.ross@gmail.com>
 */
class SearchQuery extends SelectQuery
{
    /** @var string */
    protected $search;
    /** @var SearchConfig */
    protected $config;

    /**
     * Constructor.
     */
    public function __construct(?QueryBuilder $qb = null, QueryParameterParser $parser, SearchConfig $config)
    {
        parent::__construct($qb, $parser);
        $this->config = $config;
    }

    /**
     * This method sets the search filter which then triggers the process method.
     *
     * @param string $search full search query
     *
     * @throws QueryParseException
     */
    public function setSearch($search): void
    {
        $this->search = $search;
        $this->processFilters();
    }

    /**
     * Sets the overall parameters on the query. This may include others
     * than the search query itself which gets set to the 'filter' param.
     */
    public function setParameters(array $params): void
    {
        $this->params = $params;
    }

    /**
     * Gets the individual elements of the search query as an array.
     */
    public function getSearchWords(): array
    {
        return explode(' ', $this->search);
    }

    /**
     * This is an internal helper method to get the search words prepared to
     * be passed to the expression builder.
     */
    protected function getSearchParameter(): string
    {
        if (mb_strpos($this->search, '+')) {
            $words = preg_split('/[\s\+]+/', $this->search);

            return '%' . implode('% && %', $words) . '%';
        }
        $words = explode(' ', $this->search);

        return '%' . implode('% || %', $words) . '%';
    }

    /**
     * This overrides the SelectQuery default to do some extra preparation for a search query.
     * Firstly it builds separate filters for the search query and then it removes the filter
     * from the params and the others will then get processed normally by the parent.
     *
     * @throws QueryParseException
     */
    protected function processFilters(): void
    {
        $params = $this->params;

        if (! $this->contentType) {
            throw new QueryParseException('You have attempted to run a search query without specifying a ContentType', 1);
        }

        if (isset($params['invisible']) && $params['invisible'] === true) {
            $this->config->enableSearchInvisible(true);
        }

        if (! $config = $this->config->getConfig($this->contentType)) {
            throw new QueryParseException('You have attempted to run a search query on an unknown ContentType or one that is not searchable', 1);
        }

        unset($params['filter'], $params['invisible']);

        foreach ($config as $field => $options) {
            $params[$field] = $this->getSearchParameter();
        }

        $this->params = $params;

        parent::processFilters();
    }

    /**
     * Creates a composite expression that adds all the attached
     * filters individual expressions into a combined one.
     */
    public function getWhereExpression(): ?Composite
    {
        if (! count($this->filters)) {
            return null;
        }

        $wrapExpr = $this->qb->expr()->andX();
        $config = $this->config->getConfig($this->contentType);
        $searchExpr = $this->qb->expr()->orX();
        $searchKeys = array_keys($config);

        /** @var Filter $filter */
        foreach ($this->filters as $filter) {
            if (in_array($filter->getKey(), $searchKeys, true)) {
                $searchExpr->add($filter->getExpression());
            } else {
                $wrapExpr->add($filter->getExpression());
            }
        }
        $wrapExpr->add($searchExpr);

        return $wrapExpr;
    }
}
