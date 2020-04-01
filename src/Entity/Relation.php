<?php

declare(strict_types=1);

namespace Bolt\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"get_relation"}},
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     graphql={"item_query"}
 * )
 * @ORM\Entity(repositoryClass="Bolt\Repository\RelationRepository")
 * @ORM\Table(indexes={
 *     @ORM\Index(name="name_idx", columns={"name"}),
 *     @ORM\Index(name="group_idx", columns={"group"})
 * })
 * @ApiFilter(SearchFilter::class, strategy="partial")
 */
class Relation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191)
     * @Groups("get_relation")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Content", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     *
     * @var Content
     * @Groups("get_relation")
     */
    private $fromContent;

    /**
     * @ORM\ManyToOne(targetEntity="Content", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     *
     * @var Content
     * @Groups("get_relation")
     */
    private $toContent;

    /**
     * @ORM\Column(type="integer")
     */
    private $position = 0;

    /**
     * @ORM\Column(name="`group`", type="string", length=191)
     */
    private $group;

    /**
     * Definition contains properties like:
     * - name
     * - from content type
     * - to content type(s)
     * - multiple
     * - sortable
     * - min
     * - max
     *
     * @var array
     */
    private $definition = [];

    public function __construct(Content $fromContent, Content $toContent, ?string $name = null)
    {
        $this->fromContent = $fromContent;
        $this->toContent = $toContent;
        $this->name = $name ?: $toContent->getContentTypeSlug();
        $this->group = sprintf('%s_%s', $fromContent->getContentTypeSlug(), $this->name);
        $this->setDefinitionFromContentDefinition();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getFromContent(): Content
    {
        return $this->fromContent;
    }

    public function getToContent(): Content
    {
        return $this->toContent;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function getDefinition(): array
    {
        if (empty($this->definition) === true && empty($this->fromContent) === false) {
            $this->setDefinitionFromContentDefinition();
        }

        return $this->definition;
    }

    /**
     * @see: Bolt\Event\Listener\RelationFillListener
     */
    public function setDefinitionFromContentDefinition(): void
    {
        $contentTypeDefinition = $this->fromContent->getDefinition();
        if ($contentTypeDefinition === null) {
            throw new \InvalidArgumentException('Owning Content not fully initialized');
        }

        if (isset($contentTypeDefinition['relations'][$this->getName()]) === false) {
            throw new \InvalidArgumentException('Invalid Relation name');
        }

        $this->definition = $contentTypeDefinition['relations'][$this->getName()];
    }
}
