<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Common\Str;
use Carbon\Carbon;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class TimedPublishSubscriber implements EventSubscriberInterface
{
    public const PRIORITY = 30;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $prefix;

    public function __construct(string $prefix, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->prefix = Str::ensureEndsWith($prefix, '_');
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
        $queryPublish = sprintf('update %scontent SET status = "published", published_at = :now  WHERE status = "timed" AND published_at < :now', $this->prefix);
        $queryDepublish = sprintf('update %scontent SET status = "held", depublished_at = :now WHERE status = "published" AND depublished_at < :now', $this->prefix);

        try {
            $conn->executeUpdate($queryPublish, [':now' => $now]);
            $conn->executeUpdate($queryDepublish, [':now' => $now]);
        } catch (TableNotFoundException $e) {
            // Fail silently, output user-friendly exception elsewhere.
        }
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
}
