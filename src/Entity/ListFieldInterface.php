<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Countable;

/**
 * Any Field that has an array of fields as its value must implement this interface.
 */
interface ListFieldInterface extends Countable
{
}
