<?php

declare(strict_types=1);

namespace Bolt\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Bolt\Content\FieldType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"get_content"}, "enable_max_depth"=true},
 *     denormalizationContext={"groups"={"put"}},
 *     collectionOperations={"get"},
 *     itemOperations={"get",
 *         "put"={
 *             "denormalization_context"={"groups"={"put"}},
 *         }
 *     }
 * )
 * @ORM\Entity(repositoryClass="Bolt\Repository\FieldRepository")
 * @ORM\Table(name="bolt_field")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "generic" = "field",
 *     "block" = "Bolt\Entity\Field\BlockField",
 *     "checkbox" = "Bolt\Entity\Field\CheckboxField",
 *     "date" = "Bolt\Entity\Field\DateField",
 *     "embed" = "Bolt\Entity\Field\EmbedField",
 *     "file" = "Bolt\Entity\Field\FileField",
 *     "filelist" = "Bolt\Entity\Field\FilelistField",
 *     "float" = "Bolt\Entity\Field\FloatField",
 *     "geolocation" = "Bolt\Entity\Field\GeolocationField",
 *     "hidden" = "Bolt\Entity\Field\HiddenField",
 *     "html" = "Bolt\Entity\Field\HtmlField",
 *     "image" = "Bolt\Entity\Field\ImageField",
 *     "imagelist" = "Bolt\Entity\Field\ImagelistField",
 *     "integer" = "Bolt\Entity\Field\IntegerField",
 *     "markdown" = "Bolt\Entity\Field\MarkdownField",
 *     "number" = "Bolt\Entity\Field\NumberField",
 *     "repeater" = "Bolt\Entity\Field\RepeaterField",
 *     "select" = "Bolt\Entity\Field\SelectField",
 *     "slug" = "Bolt\Entity\Field\SlugField",
 *     "templateselect" = "Bolt\Entity\Field\TemplateselectField",
 *     "text" = "Bolt\Entity\Field\TextField",
 *     "textarea" = "Bolt\Entity\Field\TextareaField",
 *     "video" = "Bolt\Entity\Field\VideoField"
 * })
 */
class Field
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"put"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191)
     * @Groups("get_content")
     */
    public $name;

    /**
     * @ORM\Column(type="json")
     * @Groups({"public", "put"})
     */
    protected $value = [];

    /**
     * @ORM\Column(type="integer")
     * @Groups({"public", "put"})
     */
    private $sortorder = 0;

    /**
     * @ORM\Column(type="string", length=191)
     * @Groups("public")
     */
    private $locale = '';

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups("public")
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity="Bolt\Entity\Content", inversedBy="fields")
     * @ORM\JoinColumn(nullable=false)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="Bolt\Entity\Field")
     * @Groups("public")
     */
    private $parent;

    /** @var ?FieldType */
    private $fieldTypeDefinition;

    public function __toString(): string
    {
        return implode(', ', $this->getValue());
    }

    public static function factory(array $definition, string $name = ''): self
    {
        $type = $definition['type'];

        $classname = '\\Bolt\\Entity\\Field\\' . ucwords($type) . 'Field';
        if (class_exists($classname)) {
            $field = new $classname();
        } else {
            $field = new self();
        }

        if (! empty($name)) {
            $field->setName($name);
        }

        $field->setDefinition($type, $definition);

        return $field;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    private function setDefinitionFromContentDefinition(): void
    {
        $contentTypeDefinition = $this->getContent()->getDefinition();

        $this->fieldTypeDefinition = FieldType::factory($this->getName(), $contentTypeDefinition);
    }

    public function getDefinition(): ?FieldType
    {
        if ($this->fieldTypeDefinition === null && $this->getContent()) {
            $this->setDefinitionFromContentDefinition();
        }

        return $this->fieldTypeDefinition;
    }

    public function setDefinition($name, array $definition): void
    {
        $this->fieldTypeDefinition = FieldType::mock($name, $definition);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->getDefinition()['type'];
    }

    public function get($key)
    {
        return isset($this->value[$key]) ? $this->value[$key] : null;
    }

    public function getValue(): ?array
    {
        return $this->value;
    }

    /**
     * like getValue() but returns single value for single value fields
     *
     * @Groups({"get_content"})
     *
     * @return array|mixed|null
     */
    public function getFieldValue()
    {
        $value = $this->getValue();
        if (is_iterable($value) && count($value) < 2) {
            return reset($value);
        }

        return $value;
    }

    public function setValue(array $value): self
    {
        $this->value = $value;

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

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
