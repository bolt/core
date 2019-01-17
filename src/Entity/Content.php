<?php

declare(strict_types=1);

namespace Bolt\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Bolt\Content\ContentType;
use Bolt\Enum\Statuses;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Tightenco\Collect\Support\Collection as LaravelCollection;

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
 * @ApiFilter(SearchFilter::class)
 * @ORM\Entity(repositoryClass="Bolt\Repository\ContentRepository")
 * @ORM\Table(name="bolt_content")
 * @ORM\HasLifecycleCallbacks
 */
class Content implements ObjectManagerAware
{
    use ContentLocalizeTrait;
    use ContentMagicTrait;

    public const NUM_ITEMS = 8; // @todo This can't be a const

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("get_content")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=191, name="contenttype")
     * @Groups("get_content")
     */
    private $contentType;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Bolt\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("put")
     */
    private $author;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", length=191)
     * @Groups("put")
     */
    private $status = null;

    /**
     * @var \DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var ?\DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("put")
     */
    private $modifiedAt = null;

    /**
     * @var ?\DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"get_content", "put"})
     */
    private $publishedAt = null;

    /**
     * @var ?\DateTimeInterface
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups("put")
     */
    private $depublishedAt = null;

    /**
     * @var Collection|Field[]
     *
     * @Groups({"put"})
     * @MaxDepth(1)
     * @ORM\OneToMany(
     *     targetEntity="Bolt\Entity\Field",
     *     mappedBy="content",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     * @ORM\OrderBy({"sortorder": "ASC"})
     */
    private $fields;

    /** @var ?ContentType */
    private $contentTypeDefinition;

    /**
     * @var Collection|Taxonomy[]
     * @Groups({"get_content", "put"})
     * @MaxDepth(1)
     *
     * @ORM\ManyToMany(targetEntity="Bolt\Entity\Taxonomy", mappedBy="content", cascade={"persist"})
     * @ORM\JoinTable(name="bolt_taxonomy_content")
     */
    private $taxonomies;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->fields = new ArrayCollection();
        $this->taxonomies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @see: Bolt\EventListener\ContentListener
     */
    public function setDefinitionFromContentTypesConfig(LaravelCollection $contentTypesConfig): void
    {
        $this->contentTypeDefinition = ContentType::factory($this->contentType, $contentTypesConfig);
    }

    public function getDefinition(): ?ContentType
    {
        return $this->contentTypeDefinition;
    }

    public function getSummary(): array
    {
        if ($this->getDefinition() === null) {
            return [];
        }

        return [
            'id' => $this->getid(),
            'contentType' => $this->getDefinition()->get('slug'),
            'slug' => $this->getSlug(),
            'title' => $this->magicTitle(),
            'excerpt' => $this->magicExcerpt(200, false),
            'image' => $this->magicImage(),
            'link' => $this->magicLink(),
            'editLink' => $this->magicEditLink(),
            'author' => [
                'id' => $this->getAuthor()->getid(),
                'displayName' => $this->getAuthor()->getDisplayName(),
                'username' => $this->getAuthor()->getusername(),
                'email' => $this->getAuthor()->getemail(),
                'roles' => $this->getAuthor()->getroles(),
            ],
            'status' => $this->getStatus(),
            'icon' => $this->getDefinition()->get('icon_one'),
            'createdAt' => $this->getCreatedAt(),
            'modifiedAt' => $this->getModifiedAt(),
            'publishedAt' => $this->getPublishedAt(),
            'depublishedAt' => $this->getDepublishedAt(),
        ];
    }

    public function getSlug(): string
    {
        return $this->get('slug')->__toString();
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @deprecated Backward-compatible alias for `getAuthor`
     */
    public function geUser(): User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    public function getStatus(): ?string
    {
        if (Statuses::isValid($this->status) === false) {
            $this->status = $this->getDefinition()->get('default_status');
        }

        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (Statuses::isValid($status)) {
            $this->status = $status;
        }

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getDepublishedAt(): ?\DateTimeInterface
    {
        return $this->depublishedAt;
    }

    public function setDepublishedAt(?\DateTimeInterface $depublishedAt): self
    {
        $this->depublishedAt = $depublishedAt;

        return $this;
    }

    /**
     * @return Field[]|Collection
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    /**
     * @Groups("get_content")
     */
    public function getFieldValues(): array
    {
        $fieldValues = [];
        foreach ($this->getFields() as $field) {
            $fieldValues[$field->getName()] = $field->getFieldValue();
        }

        return $fieldValues;
    }

    public function getValue(string $fieldName): ?array
    {
        $field = $this->getField($fieldName);
        return $field ? $field->getValue() : null;
    }

    /**
     * @Groups("get_content")
     */
    public function getAuthorName(): string
    {
        return $this->getAuthor()->getDisplayName();
    }

    public function hasField(string $name): bool
    {
        return collect($this->fields)->contains('name', $name);
    }

    public function getField(string $name): ?Field
    {
        return collect($this->fields)->where('name', $name)->first();
    }

    public function addField(Field $field): self
    {
        if ($this->fields->contains($field) === false) {
            $this->fields[] = $field;
            $field->setContent($this);
        }

        return $this;
    }

    public function removeField(Field $field): self
    {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
            // set the owning side to null (unless already changed)
            if ($field->getContent() === $this) {
                $field->setContent(null);
            }
        }

        return $this;
    }

    public function getStatuses(): array
    {
        return Statuses::all();
    }

    public function getStatusOptions(): array
    {
        $options = [];

        foreach (Statuses::all() as $option) {
            $options[] = [
                'key' => $option,
                'value' => ucwords($option),
                'selected' => $option === $this->getStatus(),
            ];
        }

        return $options;
    }

    /**
     * @return Collection|Taxonomy[]
     */
    public function getTaxonomies(?string $type = null): Collection
    {
        if ($type) {
            return $this->taxonomies->filter(
                function (Taxonomy $taxonomy) use ($type) {
                    return $taxonomy->getType() === $type;
                }
            );
        }

        return $this->taxonomies;
    }

    public function addTaxonomy(Taxonomy $taxonomy): self
    {
        if ($this->taxonomies->contains($taxonomy) === false) {
            $this->taxonomies[] = $taxonomy;
            $taxonomy->addContent($this);
        }

        return $this;
    }

    public function removeTaxonomy(Taxonomy $taxonomy): self
    {
        if ($this->taxonomies->contains($taxonomy)) {
            $this->taxonomies->removeElement($taxonomy);
            $taxonomy->removeContent($this);
        }

        return $this;
    }

    public function related(): array
    {
        // @todo See Github issue https://github.com/bolt/four/issues/163
        return [];
    }
}
