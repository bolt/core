<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nette\Utils\Strings;
use Symfony\Component\Serializer\Annotation\Groups;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
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

    /**
     * @ORM\Column(type="json")
     * @Groups("get_field")
     */
    protected $value = [];

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
        return isset($this->value[$key]) ? $this->value[$key] : null;
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