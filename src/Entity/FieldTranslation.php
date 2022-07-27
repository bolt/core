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

    /** @ORM\Column(type="json") */
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

    /**
     * Used to locate the translatable entity Bolt\Entity\Field in all its child classes
     * e.g. from Bolt\Entity\Field\TextField
     */
    public static function getTranslatableEntityClass(): string
    {
        return 'Field';
    }
}
