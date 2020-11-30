<?php

declare(strict_types=1);

namespace Bolt\Event\Listener;

use Bolt\Configuration\Content\FieldType;
use Bolt\Entity\Field;
use Bolt\Entity\Field\CollectionField;
use Bolt\Entity\Field\RawPersistable;
use Bolt\Entity\Field\SetField;
use Bolt\Entity\FieldInterface;
use Bolt\Entity\FieldTranslation;
use Bolt\Repository\FieldRepository;
use Bolt\Utils\Sanitiser;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Twig\Markup;

class FieldFillListener
{
    /** @var FieldRepository */
    private $fields;

    /** @var ContentFillListener */
    private $cfl;

    /** @var Sanitiser */
    private $sanitiser;

    public function __construct(FieldRepository $fields, ContentFillListener $cfl, Sanitiser $sanitiser)
    {
        $this->fields = $fields;
        $this->cfl = $cfl;
        $this->sanitiser = $sanitiser;
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if ($entity instanceof FieldTranslation && $entity->getTranslatable() instanceof FieldInterface) {
            /** @var Field $field */
            $field = $entity->getTranslatable();

            if (! $field instanceof RawPersistable && $field->getDefinition()->get('sanitise', true)) {
                $value = $this->clean($field->getParsedValue());
                $field->setValue($value);
            }
        }
    }

    private function clean($value): array
    {
        if (! is_iterable($value)) {
            $value = [$value];
        }

        $result = [];

        foreach ($value as $key => $v) {
            if ($v instanceof Markup) {
                $v = $this->trimZeroWidthWhitespace((string) $v);
                // todo: Figure out how to preserve original encoding
                $v = new Markup($this->sanitiser->clean($v), 'UTF-8');
            } elseif (is_string($v)) {
                $v = $this->trimZeroWidthWhitespace($v);
                $v = $this->sanitiser->clean($v);
            }

            $result[$key] = $v;
        }

        return $result;
    }

    /**
     * Remove the 'zero width space' from `{{` and `}}`, added in the editor.
     */
    public function trimZeroWidthWhitespace(string $string): string
    {
        return preg_replace('/([{}])[\x{200B}-\x{200D}\x{FEFF}]([{}])/u', '$1$2', $string);
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof Field) {
            $this->fillField($entity);
        }

        if ($entity instanceof CollectionField) {
            $this->fillCollection($entity);
        }

        if ($entity instanceof SetField) {
            $this->fillSet($entity);
        }
    }

    public function fillField(Field $field): void
    {
        // Fill in the definition of the field
        $parents = $this->getParents($field);
        $this->cfl->fillContent($field->getContent());
        $contentDefinition = $field->getContent()->getDefinition();
        $field->setDefinition($field->getName(), FieldType::factory($field->getName(), $contentDefinition, $parents));
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
        $fields = $this->fields->findAllByParent($entity);
        $entity->setValue($fields);
    }

    public function fillCollection(CollectionField $entity): void
    {
        $fields = $this->intersectFieldsAndDefinition($this->fields->findAllByParent($entity), $entity->getDefinition());
        $entity->setValue($fields);
    }

    private function intersectFieldsAndDefinition(array $fields, FieldType $definition): array
    {
        return collect($fields)->filter(function (Field $field) use ($definition) {
            return $definition->get('fields') && $definition->get('fields')->has($field->getName());
        })->toArray();
    }
}
