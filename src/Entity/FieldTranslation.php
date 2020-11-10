<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;

/**
 * @ORM\Entity
 */
class FieldTranslation implements TranslationInterface
{
    use TranslationTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="json", options={"jsonb": true}) */
    protected $value = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?array
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        $this->value = (array) $value;

        return $this;
    }

    public function get($key)
    {
        return $this->value[$key] ?? null;
    }

    public function set(string $key, $value): self
    {
        $this->value[$key] = $value;

        return $this;
    }

    public function isEmpty(): bool
    {
        $field = $this->getTranslatable();

//       @todo: Find a way to check for fields that implement Countable
//        if ($field instanceof \Countable) {
//            if (Recursion::detect()) {
//                dump('rec');
//                return true;
//            }
//
//            return $field->count() === 0;
//        }

        $value = is_iterable($this->value) && array_key_exists(0, $this->value) ? $this->value[0] : $this->value;

        return empty($value);
    }

    /**
     * Used to locate the translatable entity Bolt\Entity\Field in all its child classes
     * e.g. from Bolt\Entity\Field\TextField
     */
    public static function getTranslatableEntityClass(): string
    {
        return 'Field';
    }
}
