<?php

declare(strict_types=1);

namespace Bolt\Entity;

trait IterableFieldTrait
{
    private int $iteratorCursor = 0;

    /** @var array<Field|string> */
    private array $fields = [];

    /**
     * Makes ListFieldInterface fields |length filter
     * return the number of elements in the field
     */
    public function count(): int
    {
        return count($this->getValue());
    }

    /**
     * Makes ListFieldInterface fields .length attribute
     * return the number of elements in the field
     */
    public function length(): int
    {
        return $this->count();
    }

    public function current(): Field|string
    {
        return $this->fields[$this->iteratorCursor];
    }

    public function next(): void
    {
        ++$this->iteratorCursor;
    }

    public function key(): int
    {
        return $this->iteratorCursor;
    }

    public function valid(): bool
    {
        return isset($this->fields[$this->iteratorCursor]);
    }

    public function rewind(): void
    {
        // Ensure $this->fields is initialised
        $this->fields = $this->getValue();

        $this->iteratorCursor = 0;
    }
}
