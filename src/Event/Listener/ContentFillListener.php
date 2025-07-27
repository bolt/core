<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\FieldType;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\Field\CollectionField;
use Bolt\Entity\Field\SetField;
use Bolt\Entity\User;
use Bolt\Enum\Statuses;
use Bolt\Repository\FieldRepository;
use Bolt\Repository\UserRepository;
use Bolt\Twig\ContentExtension;
use DateTime;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use InvalidArgumentException;

class ContentFillListener
{
    public function __construct(
        private readonly Config $config,
        private readonly ContentExtension $contentExtension,
        private readonly UserRepository $users,
        private readonly FieldRepository $fieldRepository,
        private readonly string $defaultLocale
    ) {
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Content) {
            $this->guaranteeUniqueSlug($entity);
        }
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Content) {
            if ($entity->getAuthor() === null) {
                $entity->setAuthor($this->guesstimateAuthor());
            }

            if ($entity->getPublishedAt() === null && $entity->getStatus() === Statuses::PUBLISHED) {
                $entity->setPublishedAt(new DateTime());
            }

            $this->guaranteeUniqueSlug($entity);
        }
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Content) {
            $this->fillContent($entity);

            foreach ($entity->getRawFields() as $rawField) {
                if ($rawField instanceof Field) {
                    $this->fillField($rawField);
                }

                if ($rawField instanceof CollectionField) {
                    $this->fillCollection($rawField);
                }

                if ($rawField instanceof SetField) {
                    $this->fillSet($rawField);
                }
            }
        }
    }

    public function fillContent(Content $entity): void
    {
        $entity->setDefinitionFromContentTypesConfig($this->config->get('contenttypes'));
        $entity->setContentExtension($this->contentExtension);
    }

    private function guesstimateAuthor(): ?User
    {
        return $this->users->getFirstAdminUser();
    }

    private function guaranteeUniqueSlug(Content $content): void
    {
        $slug = $content->getSlug();

        try {
            $slugField = $content->getField('slug');
        } catch (InvalidArgumentException) {
            $slugField = null;
        }

        $safe = true;

        if (! $slug) {
            $slug = $this->getSafeSlug($content->getContentTypeSingularSlug());
            $safe = false;
        }

        $fields = $slug ? $this->fieldRepository->findAllBySlug($slug) : null;

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
        if (! $safe) {
            $newSlug = $this->getSafeSlug($slug);
            $content->setFieldValue('slug', $newSlug);
            $this->guaranteeUniqueSlug($content);
        }
    }

    private function getSafeSlug(string $slug): string
    {
        $separator = '-';
        // If it already ends with -{number}, increase it!
        $dashNumber = '/' . $separator . "(\d+)$/";
        preg_match($dashNumber, $slug, $matches);

        if (isset($matches[1])) {
            $replacement = $separator . ((int) $matches[1] + 1);

            return preg_replace($dashNumber, $replacement, $slug);
        }

        return $slug . $separator . '1';
    }

    public function fillField(Field $field): void
    {
        // Fill in the definition of the field
        $parents = $this->getParents($field);
        $contentDefinition = $field->getContent()->getDefinition();
        $field->setDefinition($field->getName(), FieldType::factory($field->getName(), $contentDefinition, $parents));
        $field->setDefaultLocale($this->defaultLocale);

        $field->setUseDefaultLocale($this->config->get('general/localization')->get('fallback_when_missing'));
    }

    private function getParents(Field $field): array
    {
        $parents = [];

        if ($field->hasParent()) {
            $parents = $this->getParents($field->getParent());
            $parents[] = $field->getParent()->getName();
        }

        return $parents;
    }

    public function fillSet(SetField $entity): void
    {
        $fields = $this->fieldRepository->findAllByParent($entity);
        $entity->setValue($fields);
    }

    public function fillCollection(CollectionField $entity): void
    {
        $fields = $this->intersectFieldsAndDefinition($this->fieldRepository->findAllByParent($entity), $entity->getDefinition());
        $entity->setValue($fields);
    }

    private function intersectFieldsAndDefinition(array $fields, FieldType $definition): array
    {
        return collect($fields)->filter(fn (Field $field): bool => $definition->get('fields')
            && $definition->get('fields')->has($field->getName()))->values()->toArray();
    }
}
