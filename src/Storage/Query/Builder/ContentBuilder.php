<?php

namespace Bolt\Storage\Query\Builder;

use Bolt\Storage\Query\Builder\Filter\GraphFilter;
use Exception;

class ContentBuilder implements GraphBuilderInterface
{
    private $contentName;

    private $firstRecords = 0;

    private $lastRecords = 0;

    private $limit;

    private $order = [];

    /**
     * @var array<GraphFilter>
     */
    private $filters = [];

    private $fields = [];

    public function __construct(string $contentName)
    {
        $this->contentName = $contentName;
    }

    public static function create(string $contentName): self
    {
        return new self($contentName);
    }

    public function setFirstRecords(int $firstRecords): self
    {
        $this->firstRecords = $firstRecords;

        return $this;
    }

    public function setLastRecords(int $lastRecords): self
    {
        $this->lastRecords = $lastRecords;

        return $this;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function setOrder(string $fieldName, string $orderType = 'ASC'): self
    {
        $this->order = [$fieldName, $orderType];

        return $this;
    }

    public function addFilter(GraphFilter $filter): self
    {
        $this->filters[] = $filter;
        return $this;
    }

    public function selectFields(...$fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    public function getQuery(): string
    {
        $fields = join(' ', $this->fields);
        if (empty($this->filters)) {
            return sprintf('%s { %s }', $this->contentName, $fields);
        }

        return sprintf('%s (%s) { %s }', $this->contentName, $this->getCondition(), $fields);
    }

    private function getCondition(): string
    {
        $conditions = [];

        if ($this->firstRecords !== 0 && $this->lastRecords !== 0) {
            throw new Exception();
        }

        if ($this->firstRecords > 0) {
            $conditions[] = sprintf('first: %d', $this->firstRecords);
        }

        if ($this->lastRecords > 0) {
            $conditions[] = sprintf('last: %d', $this->firstRecords);
            $conditions[] = sprintf('order: ["%s", "%s"]', 'id', 'DESC');
        }

        if ($this->lastRecords === 0 && empty($this->order) === false) {
            [$field, $order] = $this->order;
            $conditions[] = sprintf('order: ["%s", "%s"]', $field, $order);
        }

        if (empty($this->filters) === false) {
            $filterCount = count($this->filters);
            switch ($filterCount) {
                case 1:
                    $conditions[] = sprintf('filter: %s', join(',', $this->filters));
                    break;
                default:
                    $conditions[] = sprintf('filter: {%s}', join(',', $this->filters));
                    break;
            }

        }

        return join(',', $conditions);
    }
}