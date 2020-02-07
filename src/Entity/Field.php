<?php

declare(strict_types=1);

namespace Bolt\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Bolt\Configuration\Content\FieldType;
use Bolt\Utils\Sanitiser;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Tightenco\Collect\Support\Collection as LaravelCollection;
use Twig\Markup;

/**
 * @ApiResource(subresourceOperations={
 *     "api_contents_fields_get_subresource"={
 *         "method"="GET",
 *         "normalization_context"={"groups"={"get_field"}}
 *     }
 * })
 * @ORM\Entity(repositoryClass="Bolt\Repository\FieldRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string", length=191)
 * @ORM\DiscriminatorMap({"generic" = "Field"})
 */
class Field implements FieldInterface, TranslatableInterface
{
    use TranslatableTrait;

    public const TYPE = 'generic';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id = 0;

    /**
     * @ORM\Column(type="string", length=191)
     * @Groups("get_field")
     */
    public $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $sortorder = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity="Bolt\Entity\Content", inversedBy="fields")
     * @ORM\JoinColumn(nullable=false)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="Bolt\Entity\Field", cascade={"persist"})
     */
    private $parent;

    /**
     * @var ?FieldType
     */
    private $fieldTypeDefinition;

    public function __toString(): string
    {
        return (string) $this->getTwigValue();
    }

    public function __call(string $key = '', array $arguments = [])
    {
        $value = $this->getTwigValue();

        if (is_array($value) && array_key_exists($key, $value)) {
            return $value[$key];
        }

        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDefinition(): FieldType
    {
        if ($this->fieldTypeDefinition === null) {
            $this->setDefinitionFromContentDefinition();
        }

        return $this->fieldTypeDefinition;
    }

    private function setDefinitionFromContentDefinition(): void
    {
        if ($this->getContent()) {
            $contentTypeDefinition = $this->getContent()->getDefinition();
            $this->fieldTypeDefinition = FieldType::factory($this->getName(), $contentTypeDefinition);
        } else {
            $this->fieldTypeDefinition = FieldType::mock($this->getName());
        }
    }

    public function setDefinition($name, LaravelCollection $definition): void
    {
        $this->fieldTypeDefinition = FieldType::mock($name, $definition);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function get($key)
    {
        if ($this->isNew() && $this->getDefaultValue() !== null) {
            return $this->getDefaultValue()->get($key);
        }

        return $this->translate($this->getCurrentLocale(), ! $this->isTranslatable())->get($key);
    }

    /**
     * @Groups("get_field")
     */
    public function getValue(): ?array
    {
        return $this->translate($this->getCurrentLocale(), ! $this->isTranslatable())->getValue();
    }

    /**
     * Returns the default value option
     */
    public function getDefaultValue()
    {
        return $this->getDefinition()->get('default', null);
    }

    public function isNew(): bool
    {
        return $this->getId() === 0;
    }

    /**
     * like getValue() but returns single value for single value fields
     *
     * @return array|mixed|null
     */
    public function getParsedValue()
    {
        $value = $this->getValue();
        if (is_iterable($value)) {
            $count = count($value);
            if ($count === 0) {
                return null;
            } elseif ($count === 1 && array_keys($value)[0] === 0) {
                return reset($value);
            }
        }

        return $value;
    }

    /**
     * @return string|array|Markup
     */
    public function getTwigValue()
    {
        $value = $this->getParsedValue();

        if (is_string($value) && $this->getDefinition()->get('sanitise')) {
            $sanitiser = new Sanitiser();
            $value = $sanitiser->clean($value);
        }

        if (is_string($value) && $this->getDefinition()->get('allow_html')) {
            $value = new Markup($value, 'UTF-8');
        }

        return $value;
    }

    public function set(string $key, $value): self
    {
        $this->translate($this->getCurrentLocale(), ! $this->isTranslatable())->set($key, $value);

        return $this;
    }

    public function setValue($value): self
    {
        $this->translate($this->getLocale(), ! $this->isTranslatable())->setValue($value);
        $this->mergeNewTranslations();

        return $this;
    }

    public function getSortorder(): ?int
    {
        return $this->sortorder;
    }

    public function setSortorder(int $sortorder): self
    {
        $this->sortorder = $sortorder;

        return $this;
    }

    public function setLocale(?string $locale): self
    {
        $this->setCurrentLocale($locale);

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->getCurrentLocale();
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getContent(): ?Content
    {
        return $this->content;
    }

    public function setContent(?Content $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function hasParent(): bool
    {
        return $this->parent !== null;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function setLabel(string $label): self
    {
        $this->getDefinition()->put('label', $label);

        return $this;
    }

    /**
     * @Groups("get_field")
     */
    public function getType(): string
    {
        return static::TYPE;
    }

    /**
     * Used in SelectField, to distinguish between selects for "a list of items"
     * and "select from a list of Content"
     */
    public function isContentSelect(): bool
    {
        return false;
    }

    /**
     * Used in TranslatableInterface, to locate the translation entity Bolt\Entity\FieldTranslation
     */
    public static function getTranslationEntityClass(): string
    {
        $explodedNamespace = explode('\\', self::class);
        $entityClass = array_pop($explodedNamespace);

        return '\\' . implode('\\', $explodedNamespace) . '\\' . $entityClass . 'Translation';
    }

    private function isTranslatable(): bool
    {
        return $this->getDefinition()->get('localize') === true;
    }
}
