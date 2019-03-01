<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Bolt\Content\FieldType;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Tightenco\Collect\Support\Collection as LaravelCollection;

/**
 * @ORM\Entity(repositoryClass="Bolt\Repository\FieldRepository")
 * @ORM\Table(
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(name="content_field", columns={"content_id", "name"}),
 *  })
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string", length=191)
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
class Field implements Translatable
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
     * @Groups("put")
     */
    public $name;

    /**
     * @ORM\Column(type="string", length=4294967295)
     * @Groups({"put"})
     * @Gedmo\Translatable
     *
     * @var string
     */
    protected $value;

    /**
     * @ORM\Column(type="string")
     * @Groups({"put"})
     */
    protected $fieldType;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"put"})
     */
    private $sortorder = 0;

    /**
     * @Gedmo\Locale
     *
     * @var string|null
     */
    protected $locale;

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
        return $this->getValue();
    }

    public static function factory(LaravelCollection $definition, string $name = ''): self
    {
        $type = $definition['type'];

        $classname = '\\Bolt\\Entity\\Field\\' . ucwords($type) . 'Field';
        if (class_exists($classname)) {
            $field = new $classname();
        } else {
            $field = new self();
        }

        if ($name !== '') {
            $field->setName($name);
        }

        $field->setDefinition($type, $definition);

        return $field;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDefinition(): FieldType
    {
        if ($this->fieldTypeDefinition === null && $this->getContent()) {
            $this->setDefinitionFromContentDefinition();
        }

        return $this->fieldTypeDefinition;
    }

    private function setDefinitionFromContentDefinition(): void
    {
        $contentTypeDefinition = $this->getContent()->getDefinition();
        $this->fieldTypeDefinition = FieldType::factory($this->getName(), $contentTypeDefinition);
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

    public function getType(): ?string
    {
        return $this->getDefinition()->get('type');
    }

    public function get($key)
    {
        try {
            $value = \GuzzleHttp\json_decode($this->value, true);
        } catch (\InvalidArgumentException $e) {
            return null;
        }

        return $value[$key] ?? null;
    }

    public function getValue()
    {
        if ($this->fieldType === 'array') {
            return \GuzzleHttp\json_decode($this->value, true);
        }

        return $this->value;
    }

    public function setValue(string $value): self
    {
        if ($this->fieldType === 'array') {
            $this->value = \GuzzleHttp\json_encode($value);
        } else {
            $this->value = $value;
        }

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

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
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

    public function getFieldType(): string
    {
        return $this->fieldType;
    }

    public function setFieldType(string $fieldType): void
    {
        $this->fieldType = $fieldType;
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
