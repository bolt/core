<?php

declare(strict_types=1);

namespace Bolt\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Bolt\Configuration\Content\ContentType;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"get_relation"}},
 *     collectionOperations={
 *          "get"={"security"="is_granted('api:get')"},
 *          "post"={"security"="is_granted('api:post')"}
 *     },
 *     itemOperations={
 *          "get"={"security"="is_granted('api:get')"},
 *          "put"={"security"="is_granted('api:post')"},
 *          "delete"={"security"="is_granted('api:delete')"}
 *     },
 *     graphql={
 *          "item_query"={"security"="is_granted('api:get')"},
 *          "collection_query"={"security"="is_granted('api:get')"},
 *          "create"={"security"="is_granted('api:post')"},
 *          "delete"={"security"="is_granted('api:delete')"}
 *     }
 * )
 * @ORM\Entity(repositoryClass="Bolt\Repository\RelationRepository")
 * @ORM\Table(indexes={
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
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="relationsFromThisContent", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     *
     * @Groups("get_relation")
     */
    private ?Content $fromContent;

    /**
     * @ORM\ManyToOne(targetEntity="Content", inversedBy="relationsToThisContent", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     *
     * @Groups("get_relation")
     */
    private ?Content $toContent;

    /** @ORM\Column(type="integer") */
    private int $position = 0;

    /**
     * Definition contains properties like:
     * - name
     * - from content type
     * - to content type(s)
     * - multiple
     * - sortable
     * - min
     * - max
     */
    private ContentType|array $definition = [];

    public function __construct(Content $fromContent, Content $toContent)
    {
        $this->fromContent = $fromContent;
        $this->toContent = $toContent;
        $this->setDefinitionFromContentDefinition();
        // link other side of relation - needed for code using relations
        // from the content side later (e.g. validation)
        $fromContent->addRelationsFromThisContent($this);
        $toContent->addRelationsToThisContent($this);
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFromContent(): ?Content
    {
        return $this->fromContent;
    }

    public function setFromContent(?Content $content): void
    {
        $this->fromContent = $content;
    }

    public function getToContent(): ?Content
    {
        return $this->toContent;
    }

    public function setToContent(?Content $content): void
    {
        $this->toContent = $content;
    }

    public function getDefinition(): ContentType|array
    {
        if (empty($this->definition) && $this->fromContent instanceof Content) {
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
            throw new InvalidArgumentException('Owning Content not fully initialized');
        }

        if (isset($contentTypeDefinition['relations'][$this->toContent->getContentTypeSlug()]) === false) {
            throw new InvalidArgumentException('Invalid Relation name');
        }

        $this->definition = $contentTypeDefinition['relations'][$this->toContent->getContentTypeSlug()];
    }
}
