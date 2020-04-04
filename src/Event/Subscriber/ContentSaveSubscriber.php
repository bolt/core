<?php

declare(strict_types=1);

namespace Bolt\Event\Subscriber;

use Bolt\Configuration\Config;
use Bolt\Event\ContentEvent;
use Bolt\Log\LoggerTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Cache\CacheInterface;

class ContentSaveSubscriber implements EventSubscriberInterface
{
    use LoggerTrait;

    public const PRIORITY = 100;

    /** @var CacheInterface */
    private $cache;

    /** @var Config */
    private $config;

    public function __construct(CacheInterface $cache, Config $config)
    {
        $this->cache = $cache;
        $this->config = $config;
    }

    public function postSave(ContentEvent $event): ContentEvent
    {
        // Make sure we flush the cache for the menu's
        $menus = $this->config->get('menu')->keys()->all();
        foreach ($menus as $menu) {
            $this->cache->delete('frontendmenu_' . $menu);
        }
        $this->cache->delete('backendmenu');

        // Saving an entry in the log.
        $context = [
            'content_id' => $event->getContent()->getId(),
            'content_type' => $event->getContent()->getContentType(),
            'title' => $event->getContent()->getExtras()['title'],
        ];
        $this->logger->info('Saved content "{title}" ({content_type} № {content_id})', $context);

        return $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            ContentEvent::POST_SAVE => ['postSave', self::PRIORITY],
        ];
    }
}
