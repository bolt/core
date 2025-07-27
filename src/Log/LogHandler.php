<?php

declare(strict_types=1);

namespace Bolt\Log;

use Bolt\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;

class LogHandler extends AbstractProcessingHandler
{
    public function __construct(
        protected EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    /**
     * Called when writing to our database
     */
    protected function write(array $record): void
    {
        $logEntry = new Log();
        $logEntry->setMessage($record['message']);
        $logEntry->setLevel($record['level']);
        $logEntry->setLevelName($record['level_name']);
        $logEntry->setExtra($record['extra']);
        $logEntry->setUser($record['user'] ?? null);
        $logEntry->setLocation($record['location']);
        $logEntry->setContext($record['context']);

        $this->em->persist($logEntry);
        $this->em->flush();
    }
}
