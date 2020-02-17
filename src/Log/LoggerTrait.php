<?php

declare(strict_types=1);

namespace Bolt\Log;

use Psr\Log\LoggerInterface;

trait LoggerTrait
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * @required
     */
    public function setLogger(LoggerInterface $dbLogger): void
    {
        $this->logger = $dbLogger;
    }
}
