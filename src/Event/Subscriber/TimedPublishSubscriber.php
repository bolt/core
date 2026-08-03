<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Doctrine\TablePrefixTrait;
use Bolt\Entity\Content;
use Bolt\Log\LoggerTrait;
use Carbon\Carbon;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

class TimedPublishSubscriber implements EventSubscriberInterface
{
    use LoggerTrait;
    use TablePrefixTrait;

    public const PRIORITY = 30;

    private Connection $defaultConnection;
    private string $tablePrefix;

    public function __construct($tablePrefix, ManagerRegistry $managerRegistry)
    {
        /** @var Connection $connection */
        $connection = $managerRegistry->getConnection('default');
        $this->defaultConnection = $connection;
        $this->tablePrefix = $this
            ->setTablePrefixes($tablePrefix, $managerRegistry)
            ->getTablePrefix($managerRegistry->getManager('default'));
    }

    /**
     * Kernel request listener callback.
     */
    public function onKernelRequest(): void
    {
        $conn = $this->defaultConnection;
        $now = Carbon::now('UTC');

        // Publish timed Content records when 'publish_at' has passed and Depublish published Content
        // records when 'depublish_at' has passed. Note: Placeholders in DBAL don't work for tablenames.
        $queryPublish = sprintf(
            'update %scontent SET status = \'published\', published_at = :now  WHERE status = \'timed\' AND published_at < :now',
            $this->tablePrefix
        );
        $queryDepublish = sprintf(
            'update %scontent SET status = \'held\', depublished_at = :now WHERE status = \'published\' AND depublished_at < :now',
            $this->tablePrefix
        );

        try {
            $conn->executeStatement($queryPublish, ['now' => $now], ['now' => Types::DATETIME_MUTABLE]);
            $conn->executeStatement($queryDepublish, ['now' => $now], ['now' => Types::DATETIME_MUTABLE]);
        } catch (Throwable $exception) {
            // Fail silently for the user, but log at debug level for diagnostics.
            $this->logger->debug('Failed to publish/depublish timed content', ['exception' => $exception]);
        }
    }

    /**
     * Return the events to subscribe to.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            // Right after route is matched
            KernelEvents::REQUEST => [['onKernelRequest', self::PRIORITY]],
        ];
    }
}
