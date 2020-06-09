<?php

declare(strict_types=1);

namespace Bolt\Entity;

trait ListFieldTrait
{
    /**
     * Makes ListFieldInterface fields |length filter
     * return the number of elements in the field
     */
    public function count()
    {
        return count($this->getValue());
    }
}
