<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Doctrine\TablePrefixTrait;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class TimedPublishSubscriber implements EventSubscriberInterface
{
    use TablePrefixTrait;

    public const PRIORITY = 30;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $tablePrefix;

    public function __construct($tablePrefixes, ManagerRegistry $managerRegistry)
    {
        $this->entityManager = $managerRegistry->getManager('default');
        $this->tablePrefix = $this
            ->setTablePrefixes($tablePrefixes, $managerRegistry)
            ->getTablePrefix($this->entityManager);
    }

    /**
     * Return the events to subscribe to.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', self::PRIORITY]], // Right after route is matched
        ];
    }

    /**
     * Kernel request listener callback.
     */
    public function onKernelRequest(): void
    {
        $conn = $this->entityManager->getConnection();
        $now = (new Carbon())->tz('UTC');

        // Publish timed Content records when 'publish_at' has passed and Depublish published Content
        // records when 'depublish_at' has passed. Note: Placeholders in DBAL don't work for tablenames.
        $queryPublish = sprintf(
            'update %scontent SET status = "published", published_at = :now  WHERE status = "timed" AND published_at < :now',
            $this->tablePrefix
        );
        $queryDepublish = sprintf(
            'update %scontent SET status = "held", depublished_at = :now WHERE status = "published" AND depublished_at < :now',
            $this->tablePrefix
        );

        try {
            $conn->executeUpdate($queryPublish, [':now' => $now]);
            $conn->executeUpdate($queryDepublish, [':now' => $now]);
        } catch (\Throwable $e) {
            // Fail silently, output user-friendly exception elsewhere.
        }
    }
}
