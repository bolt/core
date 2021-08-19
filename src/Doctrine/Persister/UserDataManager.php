<?php

namespace Bolt\Doctrine\Persister;

use Bolt\Event\UserEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class UserDataManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function persist($object): void
    {
        $event = new UserEvent($object);
        $this->dispatcher->dispatch($event, UserEvent::ON_PRE_SAVE);

        $this->em->persist($object);

        $event = new UserEvent($object);
        $this->dispatcher->dispatch($event, UserEvent::ON_POST_SAVE);
    }

    public function delete($object): void
    {
        $event = new UserEvent($object);
        $this->dispatcher->dispatch($event, UserEvent::ON_PRE_DELETE);

        $this->em->remove($object);

        $event = new UserEvent($object);
        $this->dispatcher->dispatch($event, UserEvent::ON_POST_DELETE);
    }

    public function flush(): void
    {
        $this->em->flush();
    }
}
