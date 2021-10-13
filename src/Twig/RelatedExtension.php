<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Repository\RelationRepository;
use Bolt\Storage\Query;
use Bolt\Utils\ContentHelper;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
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

    /** @var ContentHelper */
    private $contentHelper;

    /** @var Notifications */
    private $notifications;

    /** @var TagAwareCacheInterface */
    private $cache;

    public function __construct(RelationRepository $relationRepository, Config $config, Query $query, ContentHelper $contentHelper, Notifications $notifications, TagAwareCacheInterface $cache)
    {
        $this->relationRepository = $relationRepository;
        $this->config = $config;
        $this->query = $query;
        $this->contentHelper = $contentHelper;
        $this->notifications = $notifications;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('related', [$this, 'getRelatedContent']),
            new TwigFilter('related_by_type', [$this, 'getRelatedContentByType']),
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
            new TwigFunction('related_content_by_type', [$this, 'getRelatedContentByType']),
            new TwigFunction('first_related_content', [$this, 'getFirstRelatedContent']),
            new TwigFunction('related_options', [$this, 'getRelatedOptions']),
            new TwigFunction('related_values', [$this, 'getRelatedValues']),
        ];
    }

    /**
     * @return array name => Content[]
     */
    public function getRelatedContentByType(Content $content, bool $bidirectional = true, ?int $limit = null, bool $publishedOnly = true): array
    {
        if (! $this->checkforContent($content, 'related_by_type')) {
            return [];
        }

        $relations = $this->relationRepository->findRelations($content, null, $limit, $publishedOnly);

        return (new Collection($relations))
            ->reduce(function (array $result, Relation $relation) use ($content): array {
                $relatedContent = $this->extractContentFromRelation($relation, $content);
                if ($relatedContent !== null) {
                    if (isset($result[$relatedContent->getContentTypeSlug()]) === false) {
                        $result[$relatedContent->getContentTypeSlug()] = [];
                    }
                    $result[$relatedContent->getContentTypeSlug()][] = $relatedContent;
                }

                return $result;
            }, []);
    }

    /**
     * @return Content[]
     */
    public function getRelatedContent($content, ?string $name = null, bool $bidirectional = true, ?int $limit = null, bool $publishedOnly = true): array
    {
        if (! $this->checkforContent($content, 'related')) {
            return [];
        }

        $relations = $this->relationRepository->findRelations($content, $name, $limit, $publishedOnly);

        return (new Collection($relations))
            ->map(function (Relation $relation) use ($content) {
                return $this->extractContentFromRelation($relation, $content);
            })
            ->filter()
            ->toArray();
    }

    public function getFirstRelatedContent($content, ?string $name = null, bool $publishedOnly = true): ?Content
    {
        if (! $this->checkforContent($content, 'related_first')) {
            return null;
        }

        $relation = $this->relationRepository->findFirstRelation($content, $name, $publishedOnly);

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

    public function getRelatedOptions(string $contentTypeSlug, ?string $order = null, string $format = '', ?bool $required = false): Collection
    {
        $maxAmount = $this->config->get('maximum_listing_select', 1000);

        $contentType = $this->config->getContentType($contentTypeSlug);

        if (! $order) {
            $order = $contentType->get('order');
        }

        $cacheKey = 'relatedOptions_' . md5($contentTypeSlug . $order . $format . (string) $required . $maxAmount);

        $options = $this->cache->get($cacheKey, function (ItemInterface $item) use ($contentTypeSlug, $order, $format, $required, $maxAmount) {
            $item->tag($contentTypeSlug);

            return $this->getRelatedOptionsCache($contentTypeSlug, $order, $format, $required, $maxAmount);
        });

        return new Collection($options);
    }

    public function getRelatedOptionsCache(string $contentTypeSlug, string $order, string $format, bool $required, int $maxAmount): array
    {
        $pager = $this->query->getContent($contentTypeSlug, ['order' => $order])
            ->setMaxPerPage($maxAmount)
            ->setCurrentPage(1);

        $records = iterator_to_array($pager->getCurrentPageResults());

        $options = [];

        // We need to add this as a 'dummy' option for when the user is allowed
        // not to pick an option. This is needed, because otherwise the `select`
        // would default to the first one.
        if ($required === false) {
            $options[] = [
                'key' => '',
                'value' => '',
            ];
        }

        /** @var Content $record */
        foreach ($records as $record) {
            $options[] = [
                'key' => $record->getId(),
                'value' => $this->contentHelper->get($record, $format),
            ];
        }

        return $options;
    }

    public function getRelatedValues(Content $source, string $contentType): Collection
    {
        if (! $this->checkforContent($source, 'related_values')) {
            return new Collection([]);
        }

        if ($source->getId() === null) {
            return new Collection([]);
        }

        $content = $this->getRelatedContent($source, $contentType, true, null, false);

        $values = [];

        /** @var Content $record */
        foreach ($content as $record) {
            $values[] = $record->getId();
        }

        return new Collection($values);
    }

    private function checkforContent($content, string $keyword): bool
    {
        if (! $content instanceof Content) {
            $this->notifications->warning(
                'Incorrect usage of `' . $keyword . '`-filter or function',
                'The `' . $keyword . '`-filter or function can only be applied to a single Record.'
            );

            return false;
        }

        return true;
    }
}
