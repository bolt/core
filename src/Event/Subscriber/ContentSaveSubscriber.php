<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Event\ContentEvent;
use Bolt\Log\LoggerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ContentSaveSubscriber implements EventSubscriberInterface
{
    use LoggerTrait;

    public const PRIORITY = 100;

    public function __construct(
        private TagAwareCacheInterface $cache
    ) {
    }

    public function postSave(ContentEvent $event): ContentEvent
    {
        $this->flushCaches($event);

        // Saving an entry in the log.
        $context = [
            'content_id' => $event->getContent()->getId(),
            'content_type' => $event->getContent()->getContentType(),
            'title' => $event->getContent()->getExtras()['title'],
        ];
        $this->logger->info('Saved content "{title}" ({content_type} № {content_id})', $context);

        return $event;
    }

    public function preDelete(ContentEvent $event): ContentEvent
    {
        // Saving an entry in the log now. post_delete doesn't have content anymore.
        $context = [
            'content_id' => $event->getContent()->getId(),
            'content_type' => $event->getContent()->getContentType(),
            'title' => $event->getContent()->getExtras()['title'],
        ];

        $this->logger->info('Deleted content "{title}" ({content_type} № {content_id})', $context);

        return $event;
    }

    public function postDelete(ContentEvent $event): ContentEvent
    {
        $this->flushCaches($event);

        return $event;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentEvent::POST_SAVE => ['postSave', self::PRIORITY],
            ContentEvent::PRE_DELETE => ['preDelete', self::PRIORITY],
            ContentEvent::POST_DELETE => ['postDelete', self::PRIORITY],
        ];
    }

    private function flushCaches(ContentEvent $event): void
    {
        // Make sure we flush the cache for the menus
        $this->cache->invalidateTags([
            'backendmenu',
            'frontendmenu',
            $event->getContent()->getContentTypeSlug(),
            $event->getContent()->getCacheKey(),
        ]);
    }
}
