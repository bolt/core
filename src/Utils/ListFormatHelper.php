<?php

namespace Bolt\Utils;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
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


    public function getRelated(ContentType $contentType, int $amount, string $order): array
    {
        $order = $this->fixOrder($order);

        $query = sprintf(
            'SELECT id, list_format FROM %scontent WHERE content_type = "%s" ORDER BY %s LIMIT %d ',
            $this->prefix,
            $contentType['slug'],
            $order,
            $amount
        );

        $rows = $this->connection->fetchAllAssociative($query);

        $options = [];

        foreach ($rows as $row) {
            $options[] = ['key' => (int) $row['id'], 'value' => $row['list_format']];
        }

        return $options;
    }

    public function getSelect(string $contentType, array $params): array
    {
        $order = $this->fixOrder($params['order']);

        $amount = (int) $params['limit'];

        $query = sprintf(
            'SELECT id, list_format FROM %scontent WHERE content_type = "%s" ORDER BY %s LIMIT %d ',
            $this->prefix,
            $contentType,
            $order,
            $amount
        );

        $rows = $this->connection->fetchAllAssociative($query);

        $options = [];

        foreach ($rows as $row) {
            $options[] = ['key' => (int) $row['id'], 'value' => $row['list_format']];
        }

        return $options;
    }

    public function fixOrder(string $order)
    {
        if (mb_strpos($order, '-') === 0) {
            $direction = 'DESC';
            $order = mb_substr($order, 1);
        } elseif (mb_strpos($order, ' DESC') !== false) {
            $direction = 'DESC';
            $order = str_replace(' DESC', '', $order);
        } else {
            $order = str_replace(' ASC', '', $order);
            $direction = 'ASC';
        }

        $replacements = [
            'created' => 'created_at',
            'createdat' => 'created_at',
            'datechanged' => 'modified_at',
            'datecreated' => 'created_at',
            'datepublish' => 'published_at',
            'modified' => 'modified_at',
            'modifiedat' => 'modified_at',
            'published' => 'published_at',
            'publishedat' => 'published_at',
            '_atat' => '_at',
        ];

        $order = str_replace(array_keys($replacements), array_values($replacements), strtolower($order));

        if (!in_array($order, ['id', 'content_type', 'status', 'created_at', 'published_at', 'modified_at', 'title', 'list_format'])) {
            $order = 'title';
        }

        return $order . ' ' . $direction;
    }


}
