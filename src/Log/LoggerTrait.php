<?php

declare(strict_types=1);

namespace Bolt\Log;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait LoggerTrait
{
    protected LoggerInterface $logger;

    #[Required]
    public function setLogger(LoggerInterface $dbLogger): void
    {
        $this->logger = $dbLogger;
    }
}
