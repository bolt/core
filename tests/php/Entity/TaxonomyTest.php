<?php

declare(strict_types=1);

namespace Bolt\Tests\Entity;

use Bolt\Entity\Taxonomy;
use Bolt\Repository\TaxonomyRepository;
use Bolt\Tests\DbAwareTestCase;

class TaxonomyTest extends DbAwareTestCase
{
    /** @var TaxonomyRepository */
    private $taxonomyRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taxonomyRepository = $this->getEm()->getRepository(Taxonomy::class);
    }

    public function testFactory(): void
    {
        $taxonomy = $this->taxonomyRepository->factory('foo', 'bar', 'Bar');

        $this->assertSame('foo', $taxonomy->getType());
        $this->assertSame('bar', $taxonomy->getSlug());
        $this->assertSame('Bar', $taxonomy->getName());
    }

    public function testFactoryWithSortOrder(): void
    {
        $taxonomy = $this->taxonomyRepository->factory('foo', 'Bår', 'Pømpidöm', 1000);

        $this->assertSame('foo', $taxonomy->getType());
        $this->assertSame('baar', $taxonomy->getSlug());
        $this->assertSame('Pømpidöm', $taxonomy->getName());
        $this->assertSame(1000, $taxonomy->getSortorder());
    }

    public function testFactoryWithMinimalParameters(): void
    {
        $taxonomy = $this->taxonomyRepository->factory('foo', 'Døøp');

        $this->assertSame('foo', $taxonomy->getType());
        $this->assertSame('doeoep', $taxonomy->getSlug());
        $this->assertSame('Døøp', $taxonomy->getName());
        $this->assertSame(0, $taxonomy->getSortorder());
    }

    public function testSetSlug(): void
    {
        $taxonomy = $this->taxonomyRepository->factory('foo', 'bar', 'baz');

        $taxonomy->setSlug('Qüx');

        $this->assertSame('quex', $taxonomy->getSlug());
    }

    public function testSetName(): void
    {
        $taxonomy = $this->taxonomyRepository->factory('foo', 'bar', 'baz');

        $taxonomy->setName('Føø');

        $this->assertSame('Føø', $taxonomy->getName());

        $taxonomy->setName('bar');

        $this->assertSame('bar', $taxonomy->getName());
    }

    public function testSetSortorder(): void
    {
        $taxonomy = $this->taxonomyRepository->factory('foo', 'bar', 'baz', 1000);

        $taxonomy->setSortorder(10);

        $this->assertSame(10, $taxonomy->getSortorder());
    }
}
