<?php

namespace Bolt\Utils;

use Bolt\Configuration\Config;
use Bolt\Repository\ContentRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class ListFormatHelper
{
    /** @var Config */
    private $config;

    /** @var Connection */
    private $connection;

    /** @var string */
    private $prefix;

    /** @var ContentRepository */
    private $contentRepository;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(Config $config, Connection $connection, ContentRepository $contentRepository, string $tablePrefix = 'bolt_', EntityManagerInterface $em)
    {
        $this->config = $config;
        $this->connection = $connection;
        $this->prefix = $tablePrefix;
        $this->contentRepository = $contentRepository;
        $this->em = $em;
    }

    public function clearColumns(): bool
    {
        $query = sprintf('UPDATE %scontent SET title = "", list_format = ""', $this->prefix);
        $result = $this->connection->executeQuery($query);

        return (bool) $result;
    }

    public function updateColumns(int $limit = 100): bool
    {
        $query = sprintf('SELECT id FROM %scontent WHERE title = "" OR list_format = "" LIMIT %d', $this->prefix, $limit);

        $rows = $this->connection->fetchAllAssociative($query);

        $counter = 0;

        foreach ($rows as $row) {
            $record = $this->contentRepository->findOneById($row['id']);

            $record->setListFormat();
            $this->em->persist($record);


            $counter++;
            if ($counter > 20) {
                echo '.';
                $this->em->flush();
                $counter = 0;
            }

        }

        $this->em->flush();

        return true;
    }

}
