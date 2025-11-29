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
use Bolt\Api\ContentProcessor;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Field\Excerptable;
use Bolt\Entity\Field\ScalarCastable;
use Bolt\Entity\Field\SetField;
use Bolt\Enum\Statuses;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Bolt\Twig\ContentExtension;
use Bolt\Utils\Excerpt;
use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Illuminate\Support\Collection as LaravelCollection;
use InvalidArgumentException;
use RuntimeException;
use Stringable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Twig\Template;

#[ORM\Entity(repositoryClass: ContentRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(columns: ['content_type'], name: 'content_type_idx')]
#[ORM\Index(columns: ['status'], name: 'status_idx')]
#[ApiResource(
    operations: [
        new GetCollection(security: 'is_granted("api:get")'),
        new Get(security: 'is_granted("api:get")'),
        new Put(security: 'is_granted("api:post")'),
        new Delete(security: 'is_granted("api:delete")'),
    ],
    normalizationContext: [
        'groups' => ['get_content'],
    ],
    denormalizationContext: [
        'groups' => ['api_write'],
        'enable_max_depth' => true,
    ],
    graphQlOperations: [
        new Query(security: 'is_granted("api:get")'),
        new QueryCollection(security: 'is_granted("api:get")'),
        new Mutation(security: 'is_granted("api:post")', name: 'update_content'),
        new DeleteMutation(security: 'is_granted("api:delete")', name: 'delete_content'),
    ],
    processor: ContentProcessor::class
)]
#[ApiFilter(SearchFilter::class)]
class Content implements Stringable
{
    use ContentLocalizeTrait;
    use ContentExtrasTrait;

    #[Groups(['get_content', 'api_write'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Groups(['get_content', 'api_write'])]
    #[ORM\Column(type: Types::STRING, length: 191)]
    private ?string $contentType = null;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $author = null;

    #[Groups(['get_content', 'api_write'])]
    #[ORM\Column(type: Types::STRING, length: 191)]
    private ?string $status;

    #[Groups(['get_content', 'api_write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?DateTime $createdAt;

    #[Groups(['get_content', 'api_write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $modifiedAt = null;

    #[Groups(['get_content', 'api_write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $publishedAt = null;

    #[Groups(['get_content', 'api_write'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTime $depublishedAt = null;

    #[ORM\Column(type: Types::STRING, length: 191, nullable: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::STRING, length: 191, nullable: true)]
    private ?string $listFormat = null;

    /** @var Collection<int, Field> */
    #[MaxDepth(1)]
    #[Groups('api_write')]
    #[ORM\OneToMany(mappedBy: 'content', targetEntity: Field::class, cascade: ['persist'], fetch: 'EAGER', orphanRemoval: true, indexBy: 'id')]
    #[ORM\OrderBy(['sortorder' => 'ASC'])]
    private Collection $fields;

    /** @var Collection<int, Taxonomy> */
    #[MaxDepth(1)]
    #[ORM\ManyToMany(targetEntity: Taxonomy::class, mappedBy: 'content', cascade: ['persist'])]
    private Collection $taxonomies;

    private ?ContentType $contentTypeDefinition = null;

    /**
     * @var Collection<int, Relation>
     *
     * One content has many relations, to and from, these are relations pointing from this content.
     */
    #[ORM\OneToMany(targetEntity: Relation::class, mappedBy: 'fromContent')]
    private Collection $relationsFromThisContent;

    /**
     * @var Collection<int, Relation>
     *
     * One content has many relations, to and from, these are relations pointing to this content.
     */
    #[ORM\OneToMany(mappedBy: 'toContent', targetEntity: Relation::class)]
    private Collection $relationsToThisContent;

    public function __construct(?ContentType $contentTypeDefinition = null)
    {
        $this->createdAt = $this->convertToUTCFromLocal(new DateTime());
        $this->status = Statuses::DRAFT;
        $this->taxonomies = new ArrayCollection();
        $this->fields = new ArrayCollection();
        $this->relationsFromThisContent = new ArrayCollection();
        $this->relationsToThisContent = new ArrayCollection();

        if ($contentTypeDefinition) {
            $this->setContentType($contentTypeDefinition->getSlug());
            $this->setDefinition($contentTypeDefinition);
            $this->setFieldValue('slug', '');
        }
    }

    public function __toString(): string
    {
        $contentName = $this->getDefinition() ? $this->getContentTypeSingularName() : 'Content';
        if ($this->getId()) {
            return sprintf('%s #%d', $contentName, $this->getId());
        }

        return sprintf('New %s', $contentName);
    }

    public function setId(?int $id = null): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCacheKey(?string $locale = null): string
    {
        $key = sprintf('record-%05d', $this->getId());
        if ($locale !== null) {
            $key .= '-' . $locale;
        }

        return $key;
    }

    /**
     * @see \Bolt\Event\Listener\ContentFillListener
     */
    public function setDefinitionFromContentTypesConfig(LaravelCollection $contentTypesConfig): void
    {
        $this->contentTypeDefinition = ContentType::factory($this->contentType, $contentTypesConfig);

        if (! $this->getId()) {
            // Content is new. Set the default status.
            $this->setStatus($this->contentTypeDefinition->get('default_status', 'published'));
        }

        // Set default values.
        $this->contentTypeDefinition->get('fields')->each(function (LaravelCollection $item, string $name): void {
            if ($item->has('default') && $item->get('default') !== null) {
                if ($this->hasField($name)) {
                    // If the field already exists in the database, don't override the value. ¯\_(ツ)_/¯
                    return;
                }

                $field = FieldRepository::factory($item, $name);
                $field->setValue($field->getDefaultValue());

                if (! $this->hasField($field->getName())) {
                    $this->addField($field);
                }
            }
        });
    }

    public function setDefinition(ContentType $contentType): void
    {
        $this->contentTypeDefinition = $contentType;
    }

    #[Groups('get_definition')]
    public function getDefinition(): ?ContentType
    {
        return $this->contentTypeDefinition;
    }

    public function getSlug($locale = null): ?string
    {
        // In case the ContentType has no slug defined, we've no other option than to use the id
        if (! $this->hasField('slug')) {
            return (string) $this->getId();
        }

        if ($locale === null) {
            // get slug with locale the slug already has
            $slug = $this->getFieldValue('slug');
        } else {
            // get slug with the requested locale
            $field = $this->getField('slug');

            // @todo: Refactor this. Field.php should be able to get locale
            // without changing it for later use.
            $currentLocale = $field->getLocale();
            $field->setLocale($locale);
            $slug = $field->getParsedValue();
            $field->setLocale($currentLocale);
        }

        // if no slug exists for the current/requested locale, default fallback
        if ($slug === '') {
            $field = $this->getField('slug');

            // @todo: Refactor this. Field.php should be able to get locale
            // without changing it for later use.
            $currentLocale = $field->getLocale();
            $field->setLocale($this->getField('slug')->getDefaultLocale());
            $slug = $field->getParsedValue();
            $field->setLocale($currentLocale);
        }

        return $slug;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    public function getContentTypeSlug(): string
    {
        if ($this->getDefinition() === null) {
            throw new RuntimeException('Content not fully initialized');
        }

        return $this->getDefinition()->get('slug');
    }

    public function getContentTypeSingularSlug(): string
    {
        if ($this->getDefinition() === null) {
            throw new RuntimeException('Content not fully initialized');
        }

        return $this->getDefinition()->get('singular_slug');
    }

    public function getContentTypeName(): string
    {
        if ($this->getDefinition() === null) {
            throw new RuntimeException('Content not fully initialized');
        }

        return $this->getDefinition()->get('name') ?: $this->getContentTypeSlug();
    }

    public function getContentTypeSingularName(): string
    {
        if ($this->getDefinition() === null) {
            throw new RuntimeException('Content not fully initialized');
        }

        return $this->getDefinition()->get('singular_name') ?: $this->getContentTypeSlug();
    }

    public function hasContentTypeLocales(): bool
    {
        if ($this->getDefinition() === null) {
            throw new RuntimeException('Content not fully initialized');
        }

        return ! $this->getDefinition()->get('locales')->isEmpty();
    }

    public function getContentTypeDefaultLocale(): string
    {
        if ($this->getDefinition() === null) {
            throw new RuntimeException('Content not fully initialized');
        }

        if (! $this->hasContentTypeLocales()) {
            throw new RuntimeException('Content does not have locales defined');
        }

        return $this->getDefinition()->get('locales')->first();
    }

    public function getContentTypeIcon(): ?string
    {
        if ($this->getDefinition() === null) {
            throw new RuntimeException('Content not fully initialized');
        }

        return $this->getDefinition()->get('icon_one') ?: $this->getDefinition()->get('icon_many');
    }

    public function getAuthor(): ?User
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

        if (! $this->getPublishedAt() && $status == Statuses::PUBLISHED) {
            $this->setPublishedAt(new DateTime());
        }

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->convertToLocalFromDatabase($this->createdAt);
    }

    public function setCreatedAt(?DateTime $createdAt): self
    {
        $this->createdAt = $this->convertToUTCFromLocal($createdAt);

        return $this;
    }

    public function getModifiedAt(): ?DateTime
    {
        return $this->convertToLocalFromDatabase($this->modifiedAt);
    }

    public function setModifiedAt(?DateTime $modifiedAt): self
    {
        $this->modifiedAt = $this->convertToUTCFromLocal($modifiedAt);

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateModifiedAt(): void
    {
        $this->setModifiedAt(new DateTime());
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->convertToLocalFromDatabase($this->publishedAt);
    }

    public function setPublishedAt(?DateTime $publishedAt): self
    {
        $this->publishedAt = $this->convertToUTCFromLocal($publishedAt);

        return $this;
    }

    public function getDepublishedAt(): ?DateTime
    {
        return $this->convertToLocalFromDatabase($this->depublishedAt);
    }

    public function setDepublishedAt(?DateTime $depublishedAt): self
    {
        $this->depublishedAt = $this->convertToUTCFromLocal($depublishedAt);

        return $this;
    }

    /**
     * @return Collection|Field[]
     */
    public function getRawFields(): Collection
    {
        return $this->fields;
    }

    /**
     * @return Collection|Field[]
     */
    public function getFields(): Collection
    {
        return $this->standaloneFieldsFilter();
    }

    #[Groups('get_content')]
    public function getFieldValues(): array
    {
        $fieldValues = $this->getFieldValuesFromDefinition();

        if ($fieldValues === null) {
            // Get the fields according to the database.
            foreach ($this->getFields() as $field) {
                $fieldValues[$field->getName()] = $field->getApiValue();
            }
        }

        // Make sure we have a 'slug', even if none is defined in the contentype
        if (! array_key_exists('slug', $fieldValues)) {
            $fieldValues['slug'] = $this->getSlug();
        }

        return $fieldValues;
    }

    #[Groups('get_content')]
    public function getTaxonomyValues(): array
    {
        $taxonomyValues = [];
        foreach ($this->getTaxonomies() as $taxonomy) {
            if (isset($taxonomyValues[$taxonomy->getType()]) === false) {
                $taxonomyValues[$taxonomy->getType()] = [];
            }
            $taxonomyValues[$taxonomy->getType()][$taxonomy->getSlug()] = $taxonomy->getName();
        }

        return $taxonomyValues;
    }

    public function getFieldValue(string $fieldName): mixed
    {
        if ($this->hasField($fieldName) === false) {
            return null;
        }

        return $this->getField($fieldName)->getParsedValue();
    }

    public function setFieldValue(string $fieldName, $value, ?string $locale = null): void
    {
        if (! $this->hasField($fieldName)) {
            $this->addFieldByName($fieldName);
        }

        $field = $this->getField($fieldName);

        if ($locale !== null) {
            $field->setLocale($locale);
        }

        $field->setValue($value);
    }

    public function getField(string $fieldName): Field
    {
        if ($this->hasField($fieldName) === false) {
            throw new InvalidArgumentException(sprintf("Content does not have '%s' field", $fieldName));
        }

        return $this->standaloneFieldFilter($fieldName)->first();
    }

    public function hasField(string $fieldName, $matchTypes = false): bool
    {
        $query = $this->standaloneFieldFilter($fieldName);

        // If the field doesn't exist, we can bail here
        if ($query->isEmpty()) {
            return false;
        }

        // If $matchTypes is `false`, we can state that we do have the field
        if (! $matchTypes) {
            return true;
        }

        // Otherwise, we need to ensure the types are the same
        $fieldType = $query->first()->getType();
        $definitionType = $this->contentTypeDefinition->get('fields')->get($fieldName)['type'] ?: 'undefined';

        return $fieldType === $definitionType;
    }

    public function hasFieldDefined(string $fieldName): bool
    {
        return $this->contentTypeDefinition->get('fields')->has($fieldName);
    }

    public function addField(Field $field): self
    {
        if (! $field->hasParent() && $this->hasField($field->getName())) {
            throw new InvalidArgumentException(sprintf("Content already has '%s' field", $field->getName()));
        }

        $this->fields[] = $field;
        $field->setContent($this);

        return $this;
    }

    public function addFieldByName(string $fieldName): void
    {
        if (! $this->hasFieldDefined($fieldName)) {
            throw new Exception(sprintf("Can't set Field '%s' of '%s'. Make sure the Field is defined in the %s ContentType.", $fieldName, $this->getDefinition()->get('slug'), $this->getDefinition()->get('name')));
        }

        $definition = $this->contentTypeDefinition->get('fields')->get($fieldName);

        $field = FieldRepository::factory($definition, $fieldName);

        $this->addField($field);
    }

    public function removeField(Field $field): self
    {
        $this->fields->removeElement($field);

        // set the owning side to null (unless already changed)
        if ($field->getContent() === $this) {
            $field->setContent(null);
        }

        return $this;
    }

    #[Groups('get_content')]
    public function getAuthorName(): ?string
    {
        if ($this->getAuthor() !== null) {
            return $this->getAuthor()->getDisplayName();
        }

        return null;
    }

    public function getStatuses(): array
    {
        return Statuses::all();
    }

    public function hasTaxonomyDefined(string $taxonomyName): bool
    {
        return $this->contentTypeDefinition->get('taxonomy')->contains($taxonomyName);
    }

    /**
     * @return Collection|Taxonomy[]
     */
    public function getTaxonomies(?string $type = null): Collection
    {
        if ($type) {
            return $this->taxonomies->filter(
                fn (Taxonomy $taxonomy): bool => $taxonomy->getType() === $type
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

    /**
     * Generic getter for a record fields. Will return the field with $name.
     *
     * If $name is not found, throw an exception if it's invoked from code, and
     * return null if invoked from within a template. In templates we need to be
     * more lenient, in order to do things like `{% if record.foo %}..{% endif %}
     *
     * Note: We can not rely on `{% if record.foo is defined %}`, because it
     * always returns `true` for object properties.
     * See: https://craftcms.stackexchange.com/questions/2116/twig-is-defined-always-returning-true
     *
     * - {{ record.title }} => field named title
     * - {{ record|title }} => value of guessed title field
     * - {{ record.image }} => field named image
     * - {{ record|image }} => value of guessed image field
     */
    public function __call(string $name, array $arguments = [])
    {
        try {
            $field = $this->getField($name);
        } catch (InvalidArgumentException $e) {
            $backtrace = new LaravelCollection($e->getTrace());

            if ($backtrace->contains('class', Template::class)) {
                // Invoked from within a Template render, so be lenient.
                return null;
            }

            // Invoked from code, throw Exception
            throw new RuntimeException(sprintf('Invalid field name or method call on %s: %s', $this->__toString(), $name));
        }

        if (! $field instanceof SetField && ($field instanceof Excerptable || $field instanceof ScalarCastable)) {
            return $field->getTwigValue();
        }

        return $field;
    }

    /**
     * All date/timestamps are stored in the database in UTC. When retrieving
     * them, we get the timestamp as-is in the DB with the current local
     * timezone slapped onto it. This method converts it back to UTC, and
     * then re-applies the current local timezone to it.
     */
    private function convertToLocalFromDatabase(?DateTime $dateTime): ?DateTime
    {
        if (! $dateTime) {
            return null;
        }

        $dateTimeUTC = new DateTime($dateTime->format('Y-m-d H:i:s'), new DateTimeZone('UTC'));

        return $dateTimeUTC->setTimezone($dateTime->getTimezone());
    }

    /**
     * All date/timestamps are created in the current local timezone by default.
     * Dates/timestamps must be stored in UTC in the database. This method converts
     * the local date to UTC.
     */
    private function convertToUTCFromLocal(?DateTime $dateTime): ?DateTime
    {
        if ($dateTime instanceof DateTime && $dateTime->getTimezone()->getName() !== 'UTC') {
            $utc = new DateTimeZone('UTC');
            $dateTime->setTimezone($utc);
        }

        // Prevent dates before the year `0000`, because MySQL chokes on those
        if ($dateTime instanceof DateTime && (int) $dateTime->format('Y') < 1) {
            $dateTime = null;
        }

        return $dateTime;
    }

    /**
     * Get the current regular fields, with the fields that are not present in
     * the definition anymore filtered out
     */
    private function standaloneFieldsFilter(): Collection
    {
        $definition = $this->getDefinition();

        $keys = $definition ?
            $this->getDefinition()->get('fields')->keys()->all()
            : [];
        // If the definition is missing, we cannot filter out keys. ¯\_(ツ)_/¯

        return $this->fields->filter(fn (Field $field): bool => ! $field->hasParent() &&
            (in_array($field->getName(), $keys, true) || empty($keys)));
    }

    /**
     * Get a regular field, not being part of a Collection
     */
    private function standaloneFieldFilter(string $fieldName): Collection
    {
        return $this->fields->filter(fn (Field $field): bool => $field->getName() === $fieldName && ! $field->hasParent());
    }

    public function toArray(): array
    {
        $result = get_object_vars($this);

        if ($this->author !== null) {
            $result['author'] = [
                'id' => $this->author->getId(),
                'username' => $this->author->getUserIdentifier(),
            ];
        }

        $result['fields'] = $this->getFieldValues();

        $result['taxonomies'] = $this->getTaxonomyValues();

        unset($result['contentTypeDefinition']);
        unset($result['contentExtension']);

        return $result;
    }

    public function getRelationsFromThisContent(): Collection
    {
        return $this->relationsFromThisContent;
    }

    public function addRelationsFromThisContent(Relation $relation): self
    {
        if (! $this->relationsFromThisContent->contains($relation)) {
            $this->relationsFromThisContent[] = $relation;
            $relation->setFromContent($this);
        }

        return $this;
    }

    public function removeRelationsFromThisContent(Relation $relation): self
    {
        if ($this->relationsFromThisContent->contains($relation)) {
            $this->relationsFromThisContent->removeElement($relation);
            // set the owning side to null (unless already changed)
            if ($relation->getFromContent() === $this) {
                $relation->setFromContent(null);
            }
        }

        return $this;
    }

    public function getRelationsToThisContent(): Collection
    {
        return $this->relationsToThisContent;
    }

    public function addRelationsToThisContent(Relation $relation): self
    {
        if (! $this->relationsToThisContent->contains($relation)) {
            $this->relationsToThisContent[] = $relation;
            $relation->setToContent($this);
        }

        return $this;
    }

    public function removeRelationsToThisContent(Relation $relation): self
    {
        if ($this->relationsToThisContent->contains($relation)) {
            $this->relationsToThisContent->removeElement($relation);
            // set the owning side to null (unless already changed)
            if ($relation->getToContent() === $this) {
                $relation->setToContent(null);
            }
        }

        return $this;
    }

    private function getFieldValuesFromDefinition(): ?array
    {
        if (! $this->getDefinition() || ! $this->getDefinition()->get('fields', null)) {
            // Definition is missing.
            return null;
        }

        $fieldValues = [];

        foreach ($this->getDefinition()->get('fields') as $name => $definition) {
            if ($this->hasField($name)) {
                $field = $this->getField($name);
            } else {
                $field = FieldRepository::factory($definition, $name);
            }

            $fieldValues[$name] = $field->getApiValue();
        }

        return $fieldValues;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setTitle(): self
    {
        if ($this->contentExtension instanceof ContentExtension) {
            $this->title = Excerpt::getExcerpt($this->getExtras()['title'], 191);
        } else {
            $this->title = '';
        }

        return $this;
    }

    public function getListFormat(): ?string
    {
        return $this->listFormat;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setListFormat(): self
    {
        if ($this->contentExtension instanceof ContentExtension) {
            $this->listFormat = Excerpt::getExcerpt($this->getExtras()['listFormat'], 191);
        } else {
            $this->listFormat = '';
        }

        return $this;
    }
}
