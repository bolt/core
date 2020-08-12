<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\User;
use Bolt\Enum\Statuses;
use Bolt\Repository\FieldRepository;
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

    /** @var string */
    private $defaultLocale;

    /** @var FieldRepository */
    private $fieldRepository;

    public function __construct(Config $config, ContentExtension $contentExtension, UserRepository $users, string $defaultLocale, FieldRepository $fieldRepository)
    {
        $this->config = $config;
        $this->contentExtension = $contentExtension;
        $this->users = $users;
        $this->defaultLocale = $defaultLocale;
        $this->fieldRepository = $fieldRepository;
    }

    public function preUpdate(LifeCycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof Content) {
            $this->guaranteeUniqueSlug($entity);
        }
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

            $this->guaranteeUniqueSLug($entity);
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

    private function guaranteeUniqueSLug(Content $content): void
    {
        $slug = $content->getSlug();
        $slugField = $content->getField('slug') ?? null;

        $fields = $this->fieldRepository->findAllBySlug($slug);

        if (! $fields) {
            // No slug field with that slug exists. We're done here.
            return;
        }

        $safe = true;

        // Clone fields for each locale
        $tempFields = [];
        /** @var Field $field */
        foreach ($fields as $field) {
            foreach ($field->getTranslations() as $translation) {
                $tempFields[] = clone $field->setLocale($translation->getLocale());
            }
        }
        $fields = $tempFields;

        /** @var Field $field */
        foreach ($fields as $field) {
            // If the contenttype slugs are different, we're safe.
            if ($field->getContent()->getContentTypeSlug() !== $content->getContentTypeSlug()) {
                continue;
            }

            // If the locales are different, we're safe.
            if ($slugField && $slugField->getLocale() !== $field->getLocale()) {
                continue;
            }

            // If the content and locale is the same, we're safe.
            if ($content === $field->getContent()) {
                continue;
            }

            $safe = false;

            break;
        }

        // If we're not safe, use recursion to find safe slug
        if (! $safe && $slugField) {
            $newSlug = $this->getSafeSlug($slug);
            $slugField->setValue($newSlug);
            $this->guaranteeUniqueSLug($content);
        }
    }

    private function getSafeSlug(string $slug): string
    {
        return $slug . '-1';
    }
}
