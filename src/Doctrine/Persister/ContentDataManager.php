<?php

namespace Bolt\Doctrine\Persister;

use Bolt\Entity\Content;
use Bolt\Event\ContentEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class ContentDataManager
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManagerInterface $em)
    {
        $this->dispatcher = $eventDispatcher;
        $this->em = $em;
    }

    public function save(Content $object): void
    {
        $event = new ContentEvent($object);
        $this->dispatcher->dispatch($event, ContentEvent::PRE_SAVE);

        $this->persist($object);
        $this->flush();

        $event = new ContentEvent($object);
        $this->dispatcher->dispatch($event, ContentEvent::POST_SAVE);
    }

    public function delete(Content $object): void
    {
        $event = new ContentEvent($object);
        $this->dispatcher->dispatch($event, ContentEvent::PRE_DELETE);

        $this->remove($object);
        $this->flush();

        $event = new ContentEvent($object);
        $this->dispatcher->dispatch($event, ContentEvent::POST_DELETE);

    }

    public function persist(Content $object): void
    {
        $event = new ContentEvent($object);
        $this->dispatcher->dispatch($event, ContentEvent::PRE_PERSIST);

        /* Note: Doctrine also calls preUpdate() -> Event/Listener/FieldFillListener.php */
        $this->em->persist($object);

        $event = new ContentEvent($object);
        $this->dispatcher->dispatch($event, ContentEvent::POST_PERSIST);
    }

    public function remove(Content $object): void
    {
        $event = new ContentEvent($object);
        $this->dispatcher->dispatch($event, ContentEvent::POST_REMOVE);

        $this->em->remove($object);

        $event = new ContentEvent($object);
        $this->dispatcher->dispatch($event, ContentEvent::POST_REMOVE);
    }

    public function flush(?Content $entity = null): void
    {
        $event = new ContentEvent($entity);
        $this->dispatcher->dispatch($event, ContentEvent::PRE_FLUSH);

        $this->em->flush($event->getContent());

        $this->dispatcher->dispatch($event, ContentEvent::POST_FLUSH);
    }
}
