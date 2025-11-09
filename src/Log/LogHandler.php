<?php

declare(strict_types=1);

namespace Bolt\Log;

use Bolt\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

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
    protected function write(LogRecord $record): void
    {
        $logEntry = new Log();
        $logEntry->setMessage($record->message);
        $logEntry->setLevel($record->level->value);
        $logEntry->setLevelName($record->level->getName());
        $logEntry->setExtra($record->extra);
        /** @phpstan-ignore nullCoalesce.offset (false positive: An item to the Logger's Record added by us isn't recognized) */
        $logEntry->setUser($record['user'] ?? null);
        /** @phpstan-ignore nullCoalesce.offset (false positive: An item to the Logger's Record added by us isn't recognized) */
        $logEntry->setLocation($record['location'] ?? null);
        $logEntry->setContext($record->context);

        $this->em->persist($logEntry);
        $this->em->flush();
    }
}
