<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Doctrine\TablePrefixTrait;
use Bolt\Entity\Content;
use Carbon\Carbon;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class TimedPublishSubscriber implements EventSubscriberInterface
{
    use TablePrefixTrait;

    public const PRIORITY = 30;

    /** @var object */
    private $defaultConnection;

    /** @var string */
    private $tablePrefix;

    public function __construct($tablePrefix, ManagerRegistry $managerRegistry)
    {
        $this->defaultConnection = $managerRegistry->getConnection('default');
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
        $now = (new Carbon())->tz('UTC');

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
            $conn->executeUpdate($queryPublish, [':now' => $now]);
            $conn->executeUpdate($queryDepublish, [':now' => $now]);
        } catch (\Throwable $e) {
            // Fail silently, output user-friendly exception elsewhere.
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
