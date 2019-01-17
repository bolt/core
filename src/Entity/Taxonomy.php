<?php

declare(strict_types=1);

namespace Bolt\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Bolt\Utils\Str;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"get_content", "public"}},
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 * @ORM\Entity(repositoryClass="Bolt\Repository\TaxonomyRepository")
 * @ORM\Table(name="bolt_taxonomy")
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

    /**
     * @ORM\ManyToMany(targetEntity="Bolt\Entity\Content", inversedBy="taxonomies")
     * @ORM\JoinTable(name="bolt_taxonomy_content")
     */
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

    public function __construct()
    {
        $this->content = new ArrayCollection();
    }

    /**
     * @return Taxonomy
     */
    public static function factory(string $type, string $slug, ?string $name = null, int $sortorder = 0): self
    {
        $taxonomy = new self();

        $taxonomy->setType($type);
        $taxonomy->setSlug($slug);
        $taxonomy->setName($name ?: $slug);
        $taxonomy->setSortorder($sortorder);

        return $taxonomy;
    }

    public function getId(): ?int
    {
        return $this->id;
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
}
