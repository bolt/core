<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Repository\RelationRepository;
use Bolt\Storage\Query;
use Bolt\Utils\ComposeValueHelper;
use Tightenco\Collect\Support\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RelatedExtension extends AbstractExtension
{
    /** @var RelationRepository */
    private $relationRepository;

    /** @var Config */
    private $config;

    /** @var Query */
    private $query;

    public function __construct(RelationRepository $relationRepository, Config $config, Query $query)
    {
        $this->relationRepository = $relationRepository;
        $this->config = $config;
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('related', [$this, 'getRelatedContent']),
            new TwigFilter('related_all', [$this, 'getAllRelatedContent']),
            new TwigFilter('related_first', [$this, 'getFirstRelatedContent']),
            new TwigFilter('related_options', [$this, 'getRelatedOptions']),
            new TwigFilter('related_values', [$this, 'getRelatedValues']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('related_content', [$this, 'getRelatedContent']),
            new TwigFunction('all_related_content', [$this, 'getAllRelatedContent']),
            new TwigFunction('first_related_content', [$this, 'getFirstRelatedContent']),
            new TwigFunction('related_options', [$this, 'getRelatedOptions']),
            new TwigFunction('related_values', [$this, 'getRelatedValues']),
        ];
    }

    /**
     * @return array name => Content[]
     */
    public function getAllRelatedContent(Content $content, bool $bidirectional = true, ?int $limit = null, bool $publishedOnly = true): array
    {
        $relations = $this->relationRepository->findRelations($content, null, $bidirectional, $limit, $publishedOnly);

        return (new Collection($relations))
            ->reduce(function (array $result, Relation $relation) use ($content): array {
                $relatedContent = $this->extractContentFromRelation($relation, $content);
                if ($relatedContent !== null) {
                    if (isset($result[$relation->getName()]) === false) {
                        $result[$relation->getName()] = [];
                    }
                    $result[$relation->getName()][] = $relatedContent;
                }
                return $result;
            }, []);
    }

    /**
     * @return Content[]
     */
    public function getRelatedContent(Content $content, ?string $name = null, ?string $ct = null, bool $bidirectional = true, ?int $limit = null, bool $publishedOnly = true): array
    {
        $name = $name ?? $ct;

        $relations = $this->relationRepository->findRelations($content, $name, $bidirectional, $limit, $publishedOnly);

        return (new Collection($relations))
            ->map(function (Relation $relation) use ($content) {
                return $this->extractContentFromRelation($relation, $content);
            })
            ->filter()
            ->toArray();
    }

    public function getFirstRelatedContent(Content $content, ?string $name = null, ?string $ct = null, bool $bidirectional = true, bool $publishedOnly = true): ?Content
    {
        $name = $name ?? $ct;

        $relation = $this->relationRepository->findFirstRelation($content, $name, $bidirectional, $publishedOnly);

        if ($relation === null) {
            return null;
        }

        return $this->extractContentFromRelation($relation, $content);
    }

    private function extractContentFromRelation(Relation $relation, Content $source): ?Content
    {
        if ($relation->getFromContent()->getId() === $source->getId()) {
            return $relation->getToContent();
        } elseif ($relation->getToContent()->getId() === $source->getId()) {
            return $relation->getFromContent();
        }

        return null;
    }

    public function getRelatedOptions(string $contentTypeSlug, ?string $order = null, string $format = ''): Collection
    {
        $maxAmount = $this->config->get('maximum_listing_select', 1000);

        $contentType = $this->config->getContentType($contentTypeSlug);

        if (! $order) {
            $order = $contentType->get('order');
        }

        $pager = $this->query->getContent($contentTypeSlug, ['order' => $order])
            ->setMaxPerPage($maxAmount)
            ->setCurrentPage(1);

        $records = iterator_to_array($pager->getCurrentPageResults());

        $options = [];

        /** @var Content $record */
        foreach ($records as $record) {
            $options[] = [
                'key' => $record->getId(),
                'value' => ComposeValueHelper::get($record, $format),
            ];
        }

        return new Collection($options);
    }

    public function getRelatedValues(Content $source, string $contentType): Collection
    {
        if ($source->getId() === null) {
            return new Collection([]);
        }

        $content = $this->getRelatedContent($source, $contentType, null, true, null, false);

        $values = [];

        /** @var Content $record */
        foreach ($content as $record) {
            $values[] = $record->getId();
        }

        return new Collection($values);
    }
}
