<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\User;
use Bolt\Enum\Statuses;
use Bolt\Repository\UserRepository;
use Bolt\Twig\ContentExtension;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ContentFillListener
{
    /** @var Config */
    private $config;

    /** @var ContentExtension */
    private $contentExtension;

    /** @var UserRepository */
    private $users;

    public function __construct(Config $config, ContentExtension $contentExtension, UserRepository $users)
    {
        $this->config = $config;
        $this->contentExtension = $contentExtension;
        $this->users = $users;
    }

    /**
     * @deprecated
     */
    public function preUpdate(LifeCycleEventArgs $args): void
    {
        return;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof Content) {
            if ($entity->getAuthor() === null) {
                $entity->setAuthor($this->guesstimateAuthor());
            }

            if ($entity->getPublishedAt() === null && $entity->getStatus() === Statuses::PUBLISHED) {
                $entity->setPublishedAt(new \DateTime());
            }
        }
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof Content) {
            $this->fillContent($entity);
        }
    }

    public function fillContent(Content $entity): void
    {
        $entity->setDefinitionFromContentTypesConfig($this->config->get('contenttypes'));
        $entity->setContentExtension($this->contentExtension);
    }

    private function guesstimateAuthor(): User
    {
        return $this->users->getFirstAdminUser();
    }
}
