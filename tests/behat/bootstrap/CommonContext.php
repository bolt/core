<?php

declare(strict_types=1);

namespace Context;

use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class CommonContext extends MinkContext
{
    use ApiContext;
}
