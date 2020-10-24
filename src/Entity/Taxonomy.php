<?php

declare(strict_types=1);

namespace Bolt\Entity;

use Bolt\Common\Str;
use Bolt\Configuration\Content\TaxonomyType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Tightenco\Collect\Support\Collection as LaravelCollection;

/**
 * @ORM\Entity(repositoryClass="Bolt\Repository\TaxonomyRepository")
 */
class Taxonomy
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("public")
     */
    private $id;

    /** @ORM\ManyToMany(targetEntity="Bolt\Entity\Content", inversedBy="taxonomies") */
    private $content;

    /**
     * @ORM\Column(type="string", length=191)
     * @Groups({"get_content", "public"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=191)
     * @Groups({"get_content", "public"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=191)
     * @Groups({"get_content", "public"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Groups("public")
     */
    private $sortorder = 0;

    /** @var TaxonomyType|null */
    private $taxonomyTypeDefinition = null;

    public function __construct(?TaxonomyType $taxonomyTypeDefinition = null)
    {
        $this->content = new ArrayCollection();

        if ($taxonomyTypeDefinition) {
            $this->setType($taxonomyTypeDefinition->getSlug());
            $this->setDefinition($taxonomyTypeDefinition);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @see \Bolt\Event\Listener\TaxonomyFillListener
     */
    public function setDefinitionFromTaxonomyTypesConfig(LaravelCollection $taxonomyTypesConfig): void
    {
        $this->taxonomyTypeDefinition = TaxonomyType::factory($this->type, $taxonomyTypesConfig);
    }

    /**
     * @return Collection|Content[]
     */
    public function getContent(): Collection
    {
        return $this->content;
    }

    public function addContent(Content $content): self
    {
        if (! $this->content->contains($content)) {
            $this->content[] = $content;
        }

        return $this;
    }

    public function removeContent(Content $content): self
    {
        if ($this->content->contains($content)) {
            $this->content->removeElement($content);
        }

        return $this;
    }

    public function getTaxonomyTypeSlug(): string
    {
        if ($this->getDefinition() === null) {
            throw new \RuntimeException('Taxonomy not fully initialized');
        }

        return $this->getDefinition()->get('slug');
    }

    public function getTaxonomyTypeSingularSlug(): string
    {
        if ($this->getDefinition() === null) {
            throw new \RuntimeException('Taxonomy not fully initialized');
        }

        return $this->getDefinition()->get('singular_slug');
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = Str::slug($slug);

        return $this;
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

    public function getSortorder(): ?int
    {
        return $this->sortorder;
    }

    public function setSortorder(int $sortorder): self
    {
        $this->sortorder = $sortorder;

        return $this;
    }

    public function setDefinition(TaxonomyType $taxonomyType): void
    {
        $this->taxonomyTypeDefinition = $taxonomyType;
    }

    /**
     * @Groups("get_definition")
     */
    public function getDefinition(): ?TaxonomyType
    {
        return $this->taxonomyTypeDefinition;
    }
}
