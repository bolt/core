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
        $logEntry->setUser($record->extra['user'] ?? null);
        $logEntry->setLocation($record->extra['location'] ?? null);
        $extra = $record->extra;
        unset($extra['user']);
        unset($extra['location']);
        $logEntry->setExtra($extra);
        $logEntry->setContext($record->context);

        $this->em->persist($logEntry);
        $this->em->flush();
    }
}
