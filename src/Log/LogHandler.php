<?php


namespace Bolt\Log;


use Bolt\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;

class LogHandler extends AbstractProcessingHandler
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * MonologDBHandler constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * Called when writing to our database
     * @param array $record
     */
    protected function write(array $record)
    {
        $logEntry = new Log();
        $logEntry->setMessage($record['message']);
        $logEntry->setLevel($record['level']);
        $logEntry->setLevelName($record['level_name']);
        $logEntry->setExtra($record['extra']);
        $logEntry->setUser($record['user'] ?? null);
        $logEntry->setLocation($record['location'] ?? null);
        $logEntry->setContext($record['context']);

        $this->em->persist($logEntry);
        $this->em->flush();
    }
}
