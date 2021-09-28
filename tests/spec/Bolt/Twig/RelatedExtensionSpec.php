<?php

declare(strict_types=1);

namespace spec\Bolt\Twig;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\RelationRepository;
use PhpSpec\ObjectBehavior;

class RelatedExtensionSpec extends ObjectBehavior
{
    public const ORIGIN_ID = 1;
    public const RELATED_ID = 2;
    public const TEST_CT_SLUG = 'ct-slug';

    public function let(RelationRepository $relationRepository, ContentRepository $contentRepository, Config $config): void
    {
        $this->beConstructedWith($relationRepository, $contentRepository, $config);
    }

    public function it_gets_all_related_content(Content $content, RelationRepository $relationRepository, Relation $relation, Content $related): void
    {
        $relationRepository->findRelations($content, null, null, true)
            ->shouldBeCalledOnce()
            ->willReturn([$relation, $relation]);

        $relation->getName()->shouldBeCalled()->willReturn(self::TEST_CT_SLUG);
        $relation->getToContent()->shouldBeCalledTimes(2)->willReturn($related);
        $relation->getFromContent()->shouldBeCalledTimes(2)->willReturn($content);
        $content->getId()->willReturn(self::ORIGIN_ID);
        $related->getId()->willReturn(self::RELATED_ID);

        $result = $this->getRelatedContentByType($content);
        $result->shouldBeArray();
        $result->shouldHaveCount(1);
        $result[self::TEST_CT_SLUG]->shouldBeArray();
        $result[self::TEST_CT_SLUG]->shouldHaveCount(2);
        $result[self::TEST_CT_SLUG][0]->shouldBeAnInstanceOf(Content::class);
    }

    public function it_gets_related_content(Content $content, RelationRepository $relationRepository, Relation $relation, Content $related): void
    {
        $relationRepository->findRelations($content, self::TEST_CT_SLUG, null, true)
            ->shouldBeCalledOnce()
            ->willReturn([$relation]);

        $relation->getToContent()->shouldBeCalledOnce()->willReturn($related);
        $relation->getFromContent()->shouldBeCalledOnce()->willReturn($content);
        $content->getId()->willReturn(self::ORIGIN_ID);
        $related->getId()->willReturn(self::RELATED_ID);

        $result = $this->getRelatedContent($content, self::TEST_CT_SLUG);
        $result->shouldBeArray();
        $result[0]->shouldBeAnInstanceOf(Content::class);
    }

    public function it_gets_related_content_unidirectional_with_limit(Content $content, RelationRepository $relationRepository, Relation $relation, Content $related): void
    {
        $relationRepository->findRelations($content, self::TEST_CT_SLUG, 3, true)
            ->shouldBeCalledOnce()
            ->willReturn([$relation, $relation, $relation]);

        $relation->getToContent()->shouldBeCalled()->willReturn($related);
        $relation->getFromContent()->shouldBeCalled()->willReturn($content);
        $content->getId()->willReturn(self::ORIGIN_ID);
        $related->getId()->willReturn(self::RELATED_ID);

        $result = $this->getRelatedContent($content, null, self::TEST_CT_SLUG, false, 3, true);
        $result->shouldBeArray();
        $result->shouldHaveCount(3);
        $result[0]->shouldBeAnInstanceOf(Content::class);
    }

    public function it_gets_first_related_content(Content $content, RelationRepository $relationRepository, Relation $relation, Content $related): void
    {
        $relationRepository->findFirstRelation($content, self::TEST_CT_SLUG, true)
            ->shouldBeCalledOnce()
            ->willReturn($relation);

        $relation->getToContent()->shouldBeCalledOnce()->willReturn($related);
        $relation->getFromContent()->shouldBeCalledOnce()->willReturn($content);
        $content->getId()->willReturn(self::ORIGIN_ID);
        $related->getId()->willReturn(self::RELATED_ID);

        $result = $this->getFirstRelatedContent($content, self::TEST_CT_SLUG);
        $result->shouldBeAnInstanceOf(Content::class);
    }

    public function it_couldnt_find_related_content(Content $content, RelationRepository $relationRepository): void
    {
        $relationRepository->findRelations($content, null, null, true)->willReturn([]);
        $result = $this->getRelatedContent($content);
        $result->shouldBeArray();
        $result->shouldHaveCount(0);
    }

    public function it_couldnt_find_first_related_content(Content $content, RelationRepository $relationRepository): void
    {
        $relationRepository->findFirstRelation($content, null, true)->willReturn(null);
        $result = $this->getFirstRelatedContent($content);
        $result->shouldBeNull();
    }
}
