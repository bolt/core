<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Bolt\Doctrine\JsonHelper;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\ParameterTypeInferer;
use Doctrine\ORM\QueryBuilder;

/**
 *  This query class coordinates a select query build from Bolt's
 *  custom query DSL as documented here:.
 *
 *  @see https://docs.bolt.cm/templates/content-fetching
 *
 *  The resulting QueryBuilder object is then passed through to the individual
 *  field handlers where they can perform value transformations.
 *
 *  @author Ross Riley <riley.ross@gmail.com>
 */
class SelectQuery implements QueryInterface
{
    /** @var QueryBuilder */
    protected $qb;

    /** @var QueryParameterParser */
    protected $parser;

    /** @var string */
    protected $contentType;

    /** @var array */
    protected $params = [];

    /** @var Filter[] */
    protected $filters = [];

    /** @var array */
    protected $replacements = [];

    /** @var bool */
    protected $singleFetchMode = false;

    /** @var int */
    protected $index = 1;

    /** @var array */
    protected $coreFields = [
        'id',
        'createdAt',
        'modifiedAt',
        'publishedAt',
        'depublishedAt',
        'status',
    ];

    /** @var array */
    protected $referenceFields = [
        'author',
    ];

    /** @var array */
    private $referenceJoins = [];

    /** @var array */
    private $fieldJoins = [];

    /**
     * Constructor.
     */
    public function __construct(?QueryBuilder $qb = null, QueryParameterParser $parser)
    {
        $this->qb = $qb;
        $this->parser = $parser;
    }

    /**
     * Sets the ContentType that this query will run against.
     */
    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * Gets the ContentType that this query will run against.
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Sets the parameters that will filter / alter the query.
     */
    public function setParameters(array $params): void
    {
        $this->params = array_filter($params);
        $this->processFilters();
    }

    /**
     * Getter to allow access to a set parameter.
     */
    public function getParameter(string $name)
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }

        return null;
    }

    /**
     * Setter to allow writing to a named parameter.
     */
    public function setParameter(string $name, $value): void
    {
        $this->params[$name] = $value;
        $this->processFilters();
    }

    /**
     * Creates a composite expression that adds all the attached
     * filters individual expressions into a combined one.
     */
    public function getWhereExpression(): ?Base
    {
        if (! count($this->filters)) {
            return null;
        }
        $expr = $this->qb->expr()->andX();

        $this->referenceJoins = [];
        $this->fieldJoins = [];

        foreach ($this->filters as $filter) {
            if ($filter->getExpressionObject() instanceof \Doctrine\ORM\Query\Expr\Orx) {
                // todo: `|||` and `bolt_field` integration.
                $expr = $expr->add($filter->getExpression());
            } elseif (in_array($filter->getKey(), $this->coreFields, true)) {
                $expr = $expr->add($filter->getExpression());
            } elseif (in_array($filter->getKey(), $this->referenceFields, true)) {
                $this->referenceJoins[$filter->getKey()] = $filter;
                $expr = $expr->add($filter->getExpression());
            } else {
                // This means the value is stored in the `bolt_field` table
                $this->fieldJoins[$filter->getKey()] = $filter;
            }
        }

        return $expr;
    }

    /**
     * Returns all the parameters for the query.
     */
    public function getWhereParameters(): array
    {
        $params = [];
        foreach ($this->filters as $filter) {
            $params = array_merge($params, $filter->getParameters());
        }

        return $params;
    }

    /**
     * Gets all the parameters for a specific field name.
     *
     * @param string $fieldName
     *
     * @return array array of key=>value parameters
     */
    public function getWhereParametersFor($fieldName): array
    {
        return array_intersect_key(
            $this->getWhereParameters(),
            array_flip(preg_grep('/^' . $fieldName . '_\d+$/', array_keys($this->getWhereParameters())))
        );
    }

    /**
     * Sets all the parameters for a specific field name.
     *
     * @param string $key
     */
    public function setWhereParameter($key, $value): void
    {
        foreach ($this->filters as $filter) {
            if ($filter->hasParameter($key)) {
                $filter->setParameter($key, $value);
            }
        }
    }

    public function addFilter(Filter $filter): void
    {
        $this->filters[] = $filter;
    }

    /**
     * Returns all the filters attached to the query.
     *
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Part of the QueryInterface this turns all the input into a Doctrine
     * QueryBuilder object and is usually run just before query execution.
     * That allows modifications to be made to any of the parameters up until
     * query execution time.
     */
    public function build(): QueryBuilder
    {
        $query = $this->qb;
        if ($this->getWhereExpression()) {
            $query->andWhere($this->getWhereExpression());
        }
        foreach ($this->getWhereParameters() as $key => $param) {
            $query->setParameter($key, $param, ParameterTypeInferer::inferType($param));
        }

        return $query;
    }

    /**
     * Allows public access to the QueryBuilder object.
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return $this->qb;
    }

    /**
     * Allows replacing the default QueryBuilder.
     *
     * @param QueryBuilder $qb
     */
    public function setQueryBuilder($qb): void
    {
        $this->qb = $qb;
    }

    /**
     * Returns whether the query is in single fetch mode.
     */
    public function getSingleFetchMode(): bool
    {
        return $this->singleFetchMode;
    }

    /**
     * Turns single fetch mode on or off.
     */
    public function setSingleFetchMode(bool $value): void
    {
        $this->singleFetchMode = $value;
    }

    /**
     * @return string String representation of query
     */
    public function __toString(): string
    {
        $query = $this->build();

        return $query->getDQL();
    }

    /**
     * Internal method that runs the individual key/value input through
     * the QueryParameterParser. This allows complicated expressions to
     * be turned into simple sql expressions.
     *
     * @throws \Exception
     */
    protected function processFilters(): void
    {
        $this->filters = [];

        foreach ($this->params as $key => $value) {
            $this->parser->setAlias('content');

            $filter = $this->parser->getFilter($key, $value);
            if ($filter) {
                $this->addFilter($filter);
            }
        }
    }

    /**
     * Allows key-value queries for `bolt_user` (id) values.
     */
    public function doReferenceJoins(): void
    {
        foreach (array_keys($this->referenceJoins) as $key) {
            $this->qb->join('content.' . $key, $key);
        }
    }

    /**
     * Allows key-value queries for `bolt_field` values.
     */
    public function doFieldJoins(): void
    {
        $em = $this->qb->getEntityManager();

        foreach ($this->fieldJoins as $key => $filter) {
            $index = $this->getAndIncrementIndex();
            $contentAlias = 'content_' . $index;
            $fieldsAlias = 'fields_' . $index;
            $keyParam = 'field_' . $index;

            $originalLeftExpression = 'content.' . $key;

            $newLeftExpression = JsonHelper::wrapJsonFunction($fieldsAlias . '.value', null, $this->qb);

            $where = $filter->getExpression();
            $where = str_replace($originalLeftExpression, $newLeftExpression, $where);

            // Create the subselect to filter on the value of fields
            $innerQuery = $em
                ->createQueryBuilder()
                ->select($contentAlias . '.id')
                ->from(\Bolt\Entity\Content::class, $contentAlias)
                ->innerJoin($contentAlias . '.fields', $fieldsAlias)
                ->andWhere($where);

            // Unless the field to which the 'where' applies is `anyColumn`, we
            // Make certain it's narrowed down to that fieldname
            if ($key !== 'anyField') {
                $innerQuery->andWhere($fieldsAlias . '.name = :' . $keyParam);
                $this->qb->setParameter($keyParam, $key);
            }

            $this->qb
                ->andWhere($this->qb->expr()->in('content.id', $innerQuery->getDQL()));

            foreach ($filter->getParameters() as $key => $value) {
                $value = JsonHelper::wrapJsonFunction(null, $value, $this->qb);
                $this->qb->setParameter($key, $value);
            }
        }
    }

    public function setContentTypeFilter(array $contentTypes): void
    {
        $this->setContentType(current($contentTypes));

        $where = [];
        foreach ($contentTypes as $key => $contentType) {
            $where[] = 'content.contentType = :ct' . $key;
            $this->qb->setParameter('ct' . $key, str_replace('-', '_', $contentType));
        }

        $this->qb->andWhere(implode(' OR ', $where));
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function incrementIndex(): void
    {
        $this->index++;
    }

    public function getAndIncrementIndex()
    {
        $this->incrementIndex();

        return $this->getIndex();
    }
}
