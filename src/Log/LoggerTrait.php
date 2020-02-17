<?php

namespace Bolt\Log;

use Psr\Log\LoggerInterface;

trait LoggerTrait
{
    /**
     * @required
     */
    public function setLogger(LoggerInterface $dbLogger)
    {
        $this->logger = $dbLogger;
    }
}

