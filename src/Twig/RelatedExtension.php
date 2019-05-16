<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Repository\RelationRepository;
use Tightenco\Collect\Support\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class RelatedExtension extends AbstractExtension
{
    /**
     * @var RelationRepository
     */
    private $relationRepository;

    public function __construct(RelationRepository $relationRepository)
    {
        $this->relationRepository = $relationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('related', [$this, 'getRelatedContent']),
            new TwigFilter('related_all', [$this, 'getAllRelatedContent']),
            new TwigFilter('related_first', [$this, 'getFirstRelatedContent']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('related_content', [$this, 'getRelatedContent']),
            new TwigFunction('all_related_content', [$this, 'getAllRelatedContent']),
            new TwigFunction('first_related_content', [$this, 'getFirstRelatedContent']),
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
}
