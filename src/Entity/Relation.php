<?php

declare(strict_types=1);

namespace Bolt\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\Put;
use Bolt\Configuration\Content\ContentType;
use Bolt\Repository\RelationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: RelationRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: 'is_granted("api:get")'),
        new Put(security: 'is_granted("api:post")'),
        new Delete(security: 'is_granted("api:delete")'),
        new GetCollection(security: 'is_granted("api:get")'),
    ],
    normalizationContext: [
        'groups' => ['get_relation'],
    ],
    graphQlOperations: [
        new Query(security: 'is_granted("api:get")'),
        new QueryCollection(security: 'is_granted("api:get")'),
        new Mutation(security: 'is_granted("api:post")', name: 'update_relation'),
        new DeleteMutation(security: 'is_granted("api:delete")', name: 'delete_relation'),
    ]
)]
#[ApiFilter(SearchFilter::class, strategy: 'partial')]
class Relation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Groups('get_relation')]
    #[ORM\ManyToOne(targetEntity: Content::class, fetch: 'EAGER', inversedBy: 'relationsFromThisContent')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Content $fromContent;

    #[Groups('get_relation')]
    #[ORM\ManyToOne(targetEntity: Content::class, fetch: 'EAGER', inversedBy: 'relationsToThisContent')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Content $toContent;

    #[ORM\Column(type: Types::INTEGER)]
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
