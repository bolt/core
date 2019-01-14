<?php

declare(strict_types=1);

namespace Bolt\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Bolt\Configuration\Config;
use Bolt\Content\ContentType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManagerAware;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"public"}, "enable_max_depth"=true},
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
    use ContentMagicTrait;
    use ContentLocalizeTrait;

    public const NUM_ITEMS = 8; // @todo This can't be a const

    public const STATUSES = ['published', 'held', 'timed', 'draft']; // @todo Move to Enum

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("public")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, name="contenttype")
     * @Groups("public")
     */
    private $contentType;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Bolt\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("public")
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=191)
     * @Groups({"public", "put"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     * @Groups("public")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"public", "put"})
     */
    private $modifiedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"public", "put"})
     */
    private $publishedAt;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"public", "put"})
     */
    private $depublishedAt;

    /**
     * @var Field[]|ArrayCollection
     * @Groups({"public", "put"})
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

    /** @var ContentType */
    private $contentTypeDefinition;

    /**
     * @var UrlGeneratorInterface
     * @todo move out of Entity
     */
    private $urlGenerator;

    /**
     * @var Config
     * @todo move out of Entity
     */
    private $config;

    /**
     * @ORM\ManyToMany(targetEntity="Bolt\Entity\Taxonomy", mappedBy="content", cascade={"persist"})
     * @ORM\JoinTable(name="bolt_taxonomy_content")
     */
    private $taxonomies;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->modifiedAt = new \DateTime();
        $this->publishedAt = new \DateTime();
        $this->depublishedAt = new \DateTime();
        $this->fields = new ArrayCollection();
        $this->taxonomies = new ArrayCollection();
        $this->status = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @see: Bolt\EventListener\ContentListener
     */
    public function setConfig(Config $config): void
    {
        $this->config = $config;

        $this->contentTypeDefinition = ContentType::factory($this->contentType, $config->get('contenttypes'));
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->urlGenerator;
    }

    public function getDefinition()
    {
        return $this->contentTypeDefinition;
    }

    public function getSummary(): array
    {
        if (! $this->getDefinition()) {
            return [];
        }

        return [
            'id' => $this->getid(),
            'contenttype' => $this->getDefinition()->get('slug'),
            'slug' => $this->getSlug(),
            'title' => $this->magicTitle(),
            'excerpt' => $this->magicExcerpt(200, false),
            'image' => $this->magicImage(),
            'link' => $this->magicLink(),
            'editlink' => $this->magicEditLink(),
            'author' => [
                'id' => $this->getAuthor()->getid(),
                'fullName' => $this->getAuthor()->getfullName(),
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
        return (string) $this->get('slug');
    }

    public function getContenttype(): ?string
    {
        return $this->contentType;
    }

    public function setContenttype(string $contenttype): self
    {
        $this->contentType = $contenttype;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    public function getStatus(): ?string
    {
        if (! in_array($this->status, self::STATUSES, true)) {
            $this->status = $this->getDefinition()->get('default_status');
        }
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (in_array($status, self::STATUSES, true)) {
            $this->status = $status;
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
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

    public function setModifiedAt(\DateTimeInterface $modifiedAt): self
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

    public function setDepublishedAt(\DateTimeInterface $depublishedAt): self
    {
        $this->depublishedAt = $depublishedAt;

        return $this;
    }

    /**
     * @return Collection|Field[]
     */
    public function getFields(): Collection
    {
        return $this->fields;
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
        if (! $this->fields->contains($field)) {
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
        return self::STATUSES;
    }

    public function getStatusOptions()
    {
        $options = [];

        foreach (self::STATUSES as $option) {
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
        if (! empty($type)) {
            return $this->taxonomies->filter(
                function ($taxo) use ($type) {
                    return $taxo->getType() === $type;
                }
            );
        }

        return $this->taxonomies;
    }

    public function addTaxonomy(Taxonomy $taxonomy): self
    {
        if (! $this->taxonomies->contains($taxonomy)) {
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
}
