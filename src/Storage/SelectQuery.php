<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Doctrine\JsonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Base;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\Query\ParameterTypeInferer;
use Doctrine\ORM\QueryBuilder;

/**
 *  This query class coordinates a select query build from Bolt's
 *  custom query DSL as documented here:.
 *
 * @see https://docs.bolt.cm/templates/content-fetching
 *
 *  The resulting QueryBuilder object is then passed through to the individual
 *  field handlers where they can perform value transformations.
 *
 * @author Ross Riley <riley.ross@gmail.com>
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
    protected $singleFetchMode = null;

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
    protected $coreDateFields = [
        'createdAt',
        'modifiedAt',
        'publishedAt',
        'depublishedAt',
    ];

    /** @var array */
    protected $taxonomyFields = [];

    /** @var array */
    protected $referenceFields = [
        'author',
    ];

    /** @var array */
    protected $regularFields = [];

    /** @var string */
    protected $anything = 'anything';

    /** @var array */
    private $referenceJoins = [];

    /** @var array */
    private $taxonomyJoins = [];

    /** @var array */
    private $fieldJoins = [];

    /** @var Config */
    private $config;

    /**
     * Constructor.
     */
    public function __construct(?QueryBuilder $qb = null, QueryParameterParser $parser, Config $config)
    {
        $this->qb = $qb;
        $this->parser = $parser;
        $this->config = $config;

        $this->setTaxonomyFields();
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

    public function isSingleton(): bool
    {
        /** @var ContentType|null $definition */
        $definition = $this->config->get('contenttypes/' . $this->contentType);

        // We only allow this, if getSingleFetchMode wasn't explicitly set
        if ($this->getSingleFetchMode() === false) {
            return false;
        }

        return $definition ? $definition->get('singleton') : false;
    }

    public function shouldReturnSingle(): bool
    {
        // We only allow this, if getSingleFetchMode wasn't explicitly set
        if ($this->getSingleFetchMode() === false) {
            return false;
        }

        $parameters = $this->getWhereParameters();

        $id = $this->getFilter('id');
        if (! $id instanceof Filter) {
            $isSingleId = false;
        } else {
            /** @var Orx|Andx $expression */
            $expression = $id->getExpressionObject();
            $parts = $expression->getParts();

            if (count($parts) > 1) {
                // More than one part? Don't return single.
                $isSingleId = false;
            } else {
                // Only if operator is '=', then return single.
                $isSingleId = current($parts)->getOperator() === '=';
            }
        }

        // If we're in an "IdentifiedSelect", always return a single
        if (array_key_exists('slug_1', $parameters) || $isSingleId) {
            return true;
        }

        return $this->getSingleFetchMode() || $this->isSingleton();
    }

    /**
     * Sets the parameters that will filter / alter the query.
     */
    public function setParameters(array $params): void
    {
        // array_map('strtolower', $params) to change all params to lowercase.
        $this->params = array_filter(array_map('strtolower', $params));
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
        $em = $this->getQueryBuilder()->getEntityManager();

        $this->referenceJoins = [];
        $this->taxonomyJoins = [];
        $this->fieldJoins = [];

        foreach ($this->filters as $filter) {
            if (in_array($filter->getKey(), $this->coreFields, true)) {
                // For fields like `id`, `createdAt` and `status`, which are in the main `bolt_content` table
                $expr = $expr->add($this->getCoreFieldExpression($filter));
            } elseif (in_array($filter->getKey(), $this->referenceFields, true)) {
                // Special case for filtering on 'author'
                $expr = $expr->add($this->getReferenceFieldExpression($filter));
            } elseif (in_array($filter->getKey(), $this->getTaxonomyFields(), true)) {
                // For when we're using a taxonomy type in the `where`
                $expr = $expr->add($this->getTaxonomyFieldExpression($filter));
            } elseif (in_array($filter->getKey(), [$this->anything], true)) {
                // build all expressions
                // put them in a wrapper OR expression
                $anythingExpr = $this->qb->expr()->OrX();
                $core = $this->getCoreFieldExpression($filter);
                $reference = $this->getReferenceFieldExpression($filter);
                $taxonomy = $this->getTaxonomyFieldExpression($filter);
                $regular = $this->getRegularFieldExpression($filter, $em);
                $anythingExpr->addMultiple([$core, $reference, $taxonomy, $regular]);
                $expr = $expr->add($anythingExpr);
            } else {
                // This means the name / value in the `where` is stored in the `bolt_field` table
                $expr = $expr->add($this->getRegularFieldExpression($filter, $em));
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

    public function getFilter(string $key): ?Filter
    {
        return array_filter($this->filters, function (Filter $filter) use ($key) {
            return $filter->getKey() === $key;
        })[0] ?? null;
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

        $dateFields = $this->getDateFields();

        // Set the regular fields. They are needed for setting the correct param if DB does not support json.
        $this->setRegularFields();

        $whereExpression = $this->getWhereExpression();
        if ($whereExpression) {
            $query->andWhere($whereExpression);
        }

        foreach ($this->getWhereParameters() as $key => $param) {
            $fieldName = preg_replace('/(_[0-9]+)$/', '', $key);
            // Use strtotime on 'date' fields to allow selections like "today", "in 3 weeks" or "this year"
            if (in_array($fieldName, $dateFields, true) && (strtotime($param) !== false)) {
                $param = date('c', strtotime($param));
            }

            if (in_array($fieldName, $this->regularFields, true)) {
                $param = JsonHelper::wrapJsonFunction(null, $param, $query->getEntityManager()->getConnection());
            }

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
    public function getSingleFetchMode(): ?bool
    {
        return $this->singleFetchMode;
    }

    /**
     * Turns single fetch mode on or off.
     */
    public function setSingleFetchMode(?bool $value): void
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
     * Allows key-value queries for `bolt_taxonomy` (slug) values.
     */
    public function doTaxonomyJoins(): void
    {
        foreach (array_keys($this->taxonomyJoins) as $key) {
            $this->qb->join('content.taxonomies', 'taxonomies_' . $key);
        }
    }

    /**
     * Allows key-value queries for `bolt_field` values.
     */
    public function doFieldJoins(): void
    {
        foreach (array_keys($this->fieldJoins) as $key) {
            $contentAlias = 'content';
            $fieldsAlias = 'fields_' . $key;
            $translationsAlias = 'translations_' . $key;
            $this->qb
                ->leftJoin($contentAlias . '.fields', $fieldsAlias)
                ->leftJoin($fieldsAlias . '.translations', $translationsAlias);
        }
    }

    public function setContentTypeFilter(array $contentTypes): void
    {
        $this->setContentType(current($contentTypes));

        $where = [];
        foreach ($contentTypes as $key => $contentType) {
            $where[] = 'content.contentType = :ct' . $key;
            $this->qb->setParameter('ct' . $key, $contentType);
        }

        $this->qb->andWhere(implode(' OR ', $where));
    }

    private function setTaxonomyFields(): void
    {
        $taxos = $this->getConfig()->get('taxonomies');

        foreach ($taxos as $taxo) {
            $this->taxonomyFields[] = $taxo->get('slug');
        }
    }

    public function getTaxonomyFields(): array
    {
        return $this->taxonomyFields;
    }

    private function setRegularFields(): void
    {
        $this->regularFields = $this->getConfig()->get('contenttypes/' . $this->getContentType())->get('fields')->keys()->all();
    }

    private function getDateFields(): array
    {
        // Get all fields from the current contentType
        $ctFields = $this->getConfig()->get('contenttypes/' . $this->getContentType())->get('fields');

        // And get the keys of those that are `type: date` (including 'datetime')
        $dateFields = $ctFields->where('type', 'date')->keys()->all();

        return array_merge($dateFields, $this->coreDateFields);
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function incrementIndex(): void
    {
        $this->index++;
    }

    public function getCoreFields(): array
    {
        return $this->coreFields;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    private function getCoreFieldExpression(Filter $filter): string
    {
        if ($filter->getKey() !== $this->anything) {
            return $filter->getExpression();
        }

        $original = $filter->getExpression();
        $expr = $this->qb->expr()->orX();

        foreach ($this->coreFields as $core) {
            $expr->add(preg_replace('/^(content\.)(anything)/', '$1' . $core, $original));
        }

        return $expr->__toString();
    }

    private function getReferenceFieldExpression(Filter $filter): string
    {
        if ($filter->getKey() !== $this->anything) {
            $this->referenceJoins[$filter->getKey()] = $filter;

            return $filter->getExpression();
        }

        $this->referenceJoins['author'] = 'author';

        $original = $filter->getExpression();
        $expr = $this->qb->expr()->orX();

        foreach ($this->referenceFields as $reference) {
            $expr->add(preg_replace('/^(content\.)(anything)/', 'content.' . $reference, $original));
        }

        return $expr->__toString();
    }

    private function getTaxonomyFieldExpression(Filter $filter): string
    {
        $this->taxonomyJoins[$filter->getKey()] = $filter;

        return sprintf('taxonomies_%s.slug = :%s', $filter->getKey(), key($filter->getParameters()));
    }

    private function getRegularFieldExpression(Filter $filter, EntityManagerInterface $em): string
    {
        $this->fieldJoins[$filter->getKey()] = $filter;
        $expr = $this->qb->expr()->andX();

        // where clause for the value of the field
        $valueAlias = sprintf('translations_%s.value', $filter->getKey());

        $originalLeftExpression = 'content.' . $filter->getKey();
        // LOWER() added to query to enable case insensitive search of JSON  values. Used in conjunction with converting $params of setParameter() to lowercase.
        // BUG SQLSTATE[42883]: Undefined function: 7 ERROR: function lower(jsonb) does not exist
        // We want to be able to search case-insensitive, database-agnostic, have to think of a good way.. 
        $newLeftExpression = JsonHelper::wrapJsonFunction($valueAlias, null, $em->getConnection());
        $valueWhere = $filter->getExpression();
        $valueWhere = str_replace($originalLeftExpression, $newLeftExpression, $valueWhere);
        $expr->add($valueWhere);

        // @todo: Filter non-standalone fields (i.e. fields with parents)
        $null = $this->qb->expr()->isNull(sprintf('fields_%s.parent', $filter->getKey()));
        $expr->add($null);

        // where clause for the name of the field
        if (! in_array($filter->getKey(), ['anyField', $this->anything], true)) {
            // Add to DQL where clause
            $nameAlias = sprintf('fields_%s.name', $filter->getKey());
            $nameParam = 'field_' . $filter->getKey();
            $nameExpression = sprintf('%s = :%s', $nameAlias, $nameParam);
            $expr->add($nameExpression);

            // Create filter to set the parameter
            $nameFilter = new Filter();
            $nameFilter->setKey($nameParam);
            $nameFilter->setParameter($nameParam, $filter->getKey());
            $this->addFilter($nameFilter);
        }

        return $expr->__toString();
    }
}
