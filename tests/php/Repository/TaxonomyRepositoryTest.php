<?php

declare(strict_types=1);

namespace Bolt\Tests\Repository;

use Bolt\Entity\Taxonomy;
use Bolt\Tests\DbAwareTestCase;

/**
 * @todo Add represenative tests here, when methods are implemented in TaxonomyRepository
 */
class TaxonomyRepositoryTest extends DbAwareTestCase
{
    public function testSearchByType(): void
    {
        $taxonomies = $this->getEm()
            ->getRepository(Taxonomy::class)
            ->findBy(['type' => 'groups']);

        $this->assertCount(3, $taxonomies);
    }

    public function testSearchBySlug(): void
    {
        $taxonomies = $this->getEm()
            ->getRepository(Taxonomy::class)
            ->findBy(['slug' => 'fun']);

        $this->assertCount(2, $taxonomies);
    }

    public function testSearchByName(): void
    {
        $taxonomies = $this->getEm()
            ->getRepository(Taxonomy::class)
            ->findBy(['name' => 'Movies']);

        $this->assertCount(1, $taxonomies);
    }

    public function testPersistEntity(): void
    {
        $taxonomy = Taxonomy::factory('foo', 'bar');

        $this->getEm()->persist($taxonomy);
        $this->getEm()->flush();

        $taxonomies = $this->getEm()
            ->getRepository(Taxonomy::class)
            ->findBy(['type' => 'foo']);

        $this->assertCount(1, $taxonomies);

        $this->getEm()->remove($taxonomy);
        $this->getEm()->flush();

        $taxonomies = $this->getEm()
            ->getRepository(Taxonomy::class)
            ->findBy(['type' => 'foo']);

        $this->assertCount(0, $taxonomies);
    }
}
