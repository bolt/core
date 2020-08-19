<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Doctrine\JsonHelper;
use Doctrine\ORM\Query\Expr\Base;
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

        // If we're in an "IdentifiedSelect", always return a single
        if (array_key_exists('slug_1', $this->getWhereParameters())
            || array_key_exists('id_1', $this->getWhereParameters())) {
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

        $this->referenceJoins = [];
        $this->taxonomyJoins = [];
        $this->fieldJoins = [];

        foreach ($this->filters as $filter) {
            if (in_array($filter->getKey(), $this->coreFields, true)) {
                // For fields like `id`, `createdAt` and `status`, which are in the main `bolt_content` table
                $expr = $expr->add($filter->getExpression());
            } elseif (in_array($filter->getKey(), $this->referenceFields, true)) {
                // Special case for filtering on 'author'
                $this->referenceJoins[$filter->getKey()] = $filter;
                $expr = $expr->add($filter->getExpression());
            } elseif (in_array($filter->getKey(), $this->getTaxonomyFields(), true)) {
                // For when we're using a taxonomy type in the `where`
                $this->taxonomyJoins[$filter->getKey()] = $filter;
                $filterExpression = sprintf('taxonomies_%s.slug = :%s', $filter->getKey(), key($filter->getParameters()));
                $expr = $expr->add($filterExpression);
            } else {
                // This means the name / value in the `where` is stored in the `bolt_field` table
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

        $dateFields = $this->getDateFields();

        if ($this->getWhereExpression()) {
            $query->andWhere($this->getWhereExpression());
        }

        foreach ($this->getWhereParameters() as $key => $param) {
            $fieldName = current(explode('_', $key));

            // Use strtotime on 'date' fields to allow selections like "today", "in 3 weeks" or "this year"
            if (in_array($fieldName, $dateFields, true) && (strtotime($param) !== false)) {
                $param = date('c', strtotime($param));
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
        $em = $this->qb->getEntityManager();

        foreach ($this->fieldJoins as $key => $filter) {
            $index = $this->getAndIncrementIndex();
            $contentAlias = 'content_' . $index;
            $fieldsAlias = 'fields_' . $index;
            $translationsAlias = 'translations_' . $index;
            $keyParam = 'field_' . $index;

            $originalLeftExpression = 'content.' . $key;
            // LOWER() added to query to enable case insensitive search of JSON  values. Used in conjunction with converting $params of setParameter() to lowercase.
            $newLeftExpression = JsonHelper::wrapJsonFunction('LOWER(' . $translationsAlias . '.value)', null, $em->getConnection());

            $where = $filter->getExpression();
            $exactWhere = str_replace($originalLeftExpression, $newLeftExpression, $where);

            // add containsWhere to allow searching of fields with Muiltiple JSON values (eg. Selectfield with mutiple entries).
            preg_match_all('/\:([a-z]*_[0-9]+)/', $where, $matches);
            $clauses = array_map(function ($m) use ($translationsAlias) {
                return 'LOWER(' . $translationsAlias . '.value) LIKE :' . $m . '_JSON';
            }, $matches[1]);
            $containsWhere = implode(' OR ', $clauses);

            // Create the subselect to filter on the value of fields
            $innerQuery = $em
                ->createQueryBuilder()
                ->select($contentAlias . '.id')
                ->from(\Bolt\Entity\Content::class, $contentAlias)
                ->innerJoin($contentAlias . '.fields', $fieldsAlias)
                ->innerJoin($fieldsAlias . '.translations', $translationsAlias)
                ->andWhere($exactWhere)
                ->OrWhere($containsWhere);

            // Unless the field to which the 'where' applies is `anyColumn`, we
            // Make certain it's narrowed down to that fieldname
            if ($key !== 'anyField') {
                $innerQuery->andWhere($fieldsAlias . '.name = :' . $keyParam);
                $this->qb->setParameter($keyParam, $key);
            } else {
                //added to include taxonomies to be searched as part of contenttype filter at the backend and frontend if anyField param is set.
                foreach ($filter->getParameters() as $value) {
                    $innerQuery->innerJoin($contentAlias . '.taxonomies', 'taxonomies_' . $index);
                    $this->qb->setParameter($key . '_1', $value);
                    $filterExpression = sprintf('LOWER(taxonomies_%s.slug) LIKE :%s', $index, $key . '_1');
                    $innerQuery->orWhere($filterExpression);
                }
            }

            $this->qb
                ->andWhere($this->qb->expr()->in('content.id', $innerQuery->getDQL()));

            foreach ($filter->getParameters() as $key => $value) {
                $value = JsonHelper::wrapJsonFunction(null, $value, $em->getConnection());
                $this->qb->setParameter($key, $value);
                //remove % if present. Reformat JSON to work with both json enabled platforms and non json platforms.
                $this->qb->setParameter($key . '_JSON', '%"' . str_replace(['["', '"]', '%'], '', $value) . '"%');
            }
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

    public function getAndIncrementIndex()
    {
        $this->incrementIndex();

        return $this->getIndex();
    }

    public function getCoreFields(): array
    {
        return $this->coreFields;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}
