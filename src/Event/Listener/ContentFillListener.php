<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\FieldParentInterface;
use Bolt\Entity\User;
use Bolt\Enum\Statuses;
use Bolt\Repository\UserRepository;
use Bolt\Twig\ContentExtension;
use Doctrine\ORM\Event\LifecycleEventArgs;
use RuntimeException;

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

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        // FieldParentInterface fields do not store anything in them.
        // But the setValue and getValue methods are still useful in runtime.
        // So, let's clear their value.
        if ($entity instanceof Content) {
            $entity->getFields()->filter(function (Field $field) {
                // @todo: Allow sets to be cleared as well.
                // Now they cannot be cleared, because the value
                // can be used in a collection after preUpdate on
                // the set is called.
                return $field instanceof Field\CollectionField;
            })->map(function (Field $field): void {
                $field->setValue([]);
            });
        }
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof Content) {
            if ($entity->getAuthor() === null) {
                $entity->setAuthor($this->guesstimateAuthor($entity->getContentTypeName()));
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
        $this->setFieldsDefaultLocales($entity);
    }

    private function guesstimateAuthor($contenttype): User
    {
        $user = $this->users->getFirstAdminUser();

        if ($user === null) {
            throw new RuntimeException('Error persisting record of type ' . $contenttype . ' without author. Could not guesstimate author.');
        }

        return $user;
    }

    private function setFieldsDefaultLocales(Content $entity): void
    {
        foreach ($entity->getRawFields() as $field) {
            $field->setDefaultLocaleFromContent();
        }
    }
}
