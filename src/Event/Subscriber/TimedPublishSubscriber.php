<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class TimedPublishSubscriber implements EventSubscriberInterface
{
    public const PRIORITY = 30;

    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Kernel request listener callback.
     */
    public function onKernelRequest(): void
    {
        $conn = $this->entityManager->getConnection();

        // Publish timed Content records when 'publish_at' has passed.
        $conn->executeUpdate(
            'update bolt_content SET status = "published", published_at = :now  WHERE status = "timed" AND published_at < :now',
            [':now' => date('Y-m-d H:i:s')]
        );

        // Depublish published Content records when 'depublish_at' has passed.
        $conn->executeUpdate(
            'update bolt_content SET status = "held", depublished_at = "1900-01-01 10:10:10" WHERE status = "published" AND depublished_at > :now',
            [':now' => date('Y-m-d H:i:s')]
        );
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
