<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Configuration\Content\FieldType;
use Bolt\Entity\Field;
use Bolt\Entity\Field\CollectionField;
use Bolt\Entity\Field\SetField;
use Bolt\Repository\FieldRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Tightenco\Collect\Support\Collection;

class FieldFillListener
{
    /** @var FieldRepository */
    private $fields;

    /** @var ContentFillListener */
    private $cfl;

    public function __construct(FieldRepository $fields, ContentFillListener $cfl)
    {
        $this->fields = $fields;
        $this->cfl = $cfl;
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof CollectionField) {
            // By calling fillContent form here, we ensure $entity has definition.
            $this->cfl->fillContent($entity->getContent());
            $this->fillCollection($entity);
        }

        if ($entity instanceof SetField) {
            $this->cfl->fillContent($entity->getContent());
            $this->fillSet($entity);
        }
    }

    public function fillSet(SetField $entity): void
    {
        $definition = $entity->getDefinition();
        $fields = $this->intersectFieldsAndDefinition($this->fields->findAllByParent($entity), $definition);

        /** @var Field $field */
        foreach ($fields as $field) {
            $definition = $entity->getDefinition()->get('fields')[$field->getName()] ?? new Collection();
            $field->setDefinition($field->getName(), $definition);
        }

        $entity->setValue($fields);
    }

    public function fillCollection(CollectionField $entity): void
    {
        $definition = $entity->getDefinition();
        $fields = $this->intersectFieldsAndDefinition($this->fields->findAllByParent($entity), $definition);

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldDefiniton = $entity->getDefinition()->get('fields')[$field->getName()] ?? new Collection();
            $field->setDefinition($field->getName(), $fieldDefiniton);
        }

        $entity->setValue($fields);
    }

    private function intersectFieldsAndDefinition(array $fields, FieldType $definition): array
    {
        return collect($fields)->filter(function (Field $field) use ($definition) {
            return $definition->get('fields')->has($field->getName());
        })->toArray();
    }
}
