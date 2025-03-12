<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Doctrine\DBAL\Types\Types;
use Bolt\Entity\Translatable\TranslationMethodsTrait;
use Bolt\Entity\Translatable\TranslationPropertiesTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class FieldTranslation implements TranslationInterface
{
    use TranslationPropertiesTrait;
    use TranslationMethodsTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::JSON, options: ['jsonb' => true])]
    protected $value = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): array
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
        return Field::class;
    }
}
