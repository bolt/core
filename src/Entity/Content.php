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
class Content
{
    use ContentMagicTraits;

    public const NUM_ITEMS = 8;

    public const STATUSES = ['published', 'held', 'timed', 'draft'];

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

    /**
     * @var ContentType
     */
    private $contentTypeDefinition;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Config */
    private $config;

    /**
     * Set the "Magic properties for automagic population in the API.
     */
    public $magictitle;
    public $magicexcerpt;
    public $magicimage;
    public $magiclink;
    public $magiceditlink;

    /**
     * @ORM\ManyToMany(targetEntity="Bolt\Entity\Taxonomy", mappedBy="content")
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
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        $this->contentTypeDefinition = ContentType::factory($this->contentType, $config->get('contenttypes'));
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getDefinition()
    {
        return $this->contentTypeDefinition;
    }

    /**
     * @return array
     */
    public function getSummary(): array
    {
        $summary = [
            'id' => $this->getid(),
            'contenttype' => $this->getDefinition()->get('slug'),
            'slug' => $this->getSlug(),
            'title' => $this->magicTitle(),
            'excerpt' => $this->magicexcerpt(),
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
            'modifiedAt' => $this->modifiedAt(),
            'publishedAt' => $this->getPublishedAt(),
            'depublishedAt' => $this->depublishedAt(),
        ];

        return $summary;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return  (string) $this->get('slug');
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
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getField(string $name): ?Field
    {
        return collect($this->fields)->where('name', $name)->first();
    }

    public function addField(Field $field): self
    {
        if (!$this->fields->contains($field)) {
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

    /**
     * @return array
     */
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
    public function getTaxonomies(): Collection
    {
        return $this->taxonomies;
    }

    public function addTaxonomy(Taxonomy $taxonomy): self
    {
        if (!$this->taxonomies->contains($taxonomy)) {
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
