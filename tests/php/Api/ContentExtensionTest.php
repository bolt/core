<?php

declare(strict_types=1);

namespace Bolt\Tests\Api;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Bolt\Api\Extensions\ContentExtension;
use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Enum\Statuses;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

class ContentExtensionTest extends TestCase
{
    public function testRelationCollectionIsFilteredToPublishedEnds(): void
    {
        $dql = $this->applyToRelationCollection();

        // Both ends of the relation must be constrained to published content.
        // This fails if the Relation branch / filter is removed.
        self::assertStringContainsString('.fromContent', $dql);
        self::assertStringContainsString('.toContent', $dql);
        self::assertStringContainsString('relation_from.status = :status', $dql);
        self::assertStringContainsString('relation_to.status = :status', $dql);
    }

    public function testRelationItemIsFilteredToPublishedEnds(): void
    {
        $qb = $this->relationQueryBuilder();

        $this->extension()->applyToItem(
            $qb,
            $this->createMock(QueryNameGeneratorInterface::class),
            Relation::class,
            ['id' => 1]
        );

        $dql = $qb->getDQL();

        self::assertStringContainsString('relation_from.status = :status', $dql);
        self::assertStringContainsString('relation_to.status = :status', $dql);
        self::assertSame(Statuses::PUBLISHED, $qb->getParameter('status')?->getValue());
    }

    public function testContentTypeFilteringIsLeftUntouchedForOtherResources(): void
    {
        $qb = $this->relationQueryBuilder();

        // A different resource class must not get the relation filter applied.
        $this->extension()->applyToCollection(
            $qb,
            $this->createMock(QueryNameGeneratorInterface::class),
            Content::class
        );

        self::assertStringNotContainsString('relation_from', $qb->getDQL());
    }

    private function applyToRelationCollection(): string
    {
        $qb = $this->relationQueryBuilder();

        $this->extension()->applyToCollection(
            $qb,
            $this->createMock(QueryNameGeneratorInterface::class),
            Relation::class
        );

        return $qb->getDQL();
    }

    private function relationQueryBuilder(): QueryBuilder
    {
        $qb = new QueryBuilder($this->createMock(EntityManagerInterface::class));

        return $qb->select('o')->from(Relation::class, 'o');
    }

    private function extension(): ContentExtension
    {
        $config = $this->createMock(Config::class);
        $config->method('get')->with('contenttypes')->willReturn(collect([]));

        return new ContentExtension($config);
    }
}
