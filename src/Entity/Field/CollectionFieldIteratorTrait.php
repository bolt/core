<?php


namespace Bolt\Entity\Field;


use Bolt\Entity\Field;

trait CollectionFieldIteratorTrait
{
    private $iteratorCursor = 0;

    public function current(): Field
    {
        /** @var Field $field */
        $field = $this->fields[$this->iteratorCursor];
        return $field;
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
        $this->iteratorCursor = 0;
    }
}