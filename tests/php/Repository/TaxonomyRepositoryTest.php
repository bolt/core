<?php

declare(strict_types=1);

namespace Bolt\Tests\Repository;

use Bolt\Entity\Taxonomy;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaxonomyRepositoryTest extends KernelTestCase
{
    /** @var \Doctrine\ORM\EntityManager */
    private $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSearchByType(): void
    {
        $taxonomies = $this->entityManager
            ->getRepository(Taxonomy::class)
            ->findBy(['type' => 'groups']);

        $this->assertCount(3, $taxonomies);
    }

    public function testSearchBySlug(): void
    {
        $taxonomies = $this->entityManager
            ->getRepository(Taxonomy::class)
            ->findBy(['slug' => 'zombies']);

        $this->assertCount(1, $taxonomies);
    }

    public function testSearchByName(): void
    {
        $taxonomies = $this->entityManager
            ->getRepository(Taxonomy::class)
            ->findBy(['name' => 'Movies']);

        $this->assertCount(1, $taxonomies);
    }

    public function testPersistEntity(): void
    {
        $taxonomy = Taxonomy::factory('foo', 'bar');

        $this->entityManager->persist($taxonomy);
        $this->entityManager->flush();

        $taxonomies = $this->entityManager
            ->getRepository(Taxonomy::class)
            ->findBy(['type' => 'foo']);

        $this->assertCount(1, $taxonomies);

        $this->entityManager->remove($taxonomy);
        $this->entityManager->flush();

        $taxonomies = $this->entityManager
            ->getRepository(Taxonomy::class)
            ->findBy(['type' => 'foo']);

        $this->assertCount(0, $taxonomies);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}
