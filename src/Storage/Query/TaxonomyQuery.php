<?php

declare(strict_types=1);

namespace Bolt\Storage\Query;

use Bolt\Storage\Database\Schema\Table\ContentType;
use Doctrine\ORM\QueryBuilder;
use Pimple;

/**
 *  This query class coordinates a taxonomy query build.
 *
 *  The resulting set then generates proxies to various content objects
 *
 *  @author Ross Riley <riley.ross@gmail.com>
 */
class TaxonomyQuery implements QueryInterface
{
    /** @var QueryBuilder */
    protected $qb;
    /** @var array */
    protected $params = [];
    /** @var array */
    protected $contentTypes = [];
    /** @var array */
    protected $taxonomyTypes = [];
    /** @var Pimple */
    private $schema;

    /**
     * Constructor.
     */
    public function __construct(QueryBuilder $qb, Pimple $schema)
    {
        $this->qb = $qb;
        $this->schema = $schema;
    }

    /**
     * Sets the parameters that will filter / alter the query.
     */
    public function setParameters(array $params): void
    {
        $this->params = array_filter($params);
    }

    /**
     * Getter to allow access to a set parameter.
     */
    public function getParameter($name): ?array
    {
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }

        return null;
    }

    /**
     * Setter to allow writing to a named parameter.
     *
     * @param string $name
     */
    public function setParameter($name, $value): void
    {
        $this->params[$name] = $value;
    }

    /**
     * Setter to specify which content types to search on.
     */
    public function setContentTypes(array $contentTypes): void
    {
        $this->contentTypes = $contentTypes;
    }

    /**
     * Setter to specify which taxonomy types to search on.
     */
    public function setTaxonomyTypes(array $taxonomyTypes): void
    {
        $this->taxonomyTypes = $taxonomyTypes;
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
        $this->buildJoin();
        $this->buildWhere();

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
     */
    public function setQueryBuilder(QueryBuilder $qb): void
    {
        $this->qb = $qb;
    }

    /**
     * @return string String representation of query
     */
    public function __toString(): string
    {
        $query = $this->build();

        return $query->getSQL();
    }

    protected function buildJoin(): void
    {
        $subQuery = '(SELECT ';
        $fragments = [];
        foreach ($this->contentTypes as $content) {
            /** @var ContentType $table */
            $table = $this->schema[$content];
            $tableName = $table->getTableName();
            $fragments[] = "id,status, '${content}' AS tablename FROM " . $tableName;
        }
        $subQuery .= implode(' UNION SELECT ', $fragments);
        $subQuery .= ')';

        $this->qb->from($subQuery, 'content');
        $this->qb->addSelect('content.*');
    }

    protected function buildWhere(): void
    {
        $params = [];
        $i = 0;
        $where = $this->qb->expr()->andX();
        foreach ($this->taxonomyTypes as $name => $slug) {
            $where->add($this->qb->expr()->eq('taxonomy.taxonomytype', ':taxonomytype_' . $i));
            $where->add($this->qb->expr()->eq('taxonomy.slug', ':slug_' . $i));
            $params['taxonomytype_' . $i] = $name;
            $params['slug_' . $i] = $slug;
            ++$i;
        }
        $this->qb->where($where)->setParameters($params);
        $this->qb->andWhere("content.status='published'");
        $this->qb->andWhere('taxonomy.contenttype=content.tablename');
        $this->qb->andWhere('taxonomy.content_id=content.id');
    }
}
