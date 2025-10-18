<?php

declare(strict_types=1);

namespace Bolt\Log;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait LoggerTrait
{
    /** @var LoggerInterface */
    protected $logger;

    #[Required]
    public function setLogger(LoggerInterface $dbLogger): void
    {
        $this->logger = $dbLogger;
    }
}
