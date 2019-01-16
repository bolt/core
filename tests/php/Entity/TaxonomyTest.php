<?php

declare(strict_types=1);

namespace Bolt\Tests\Entity;

use Bolt\Entity\Taxonomy;

class TaxonomyTest extends \PHPUnit\Framework\TestCase
{
    public function testFactory(): void
    {
        $taxonomy = Taxonomy::factory('foo', 'bar', 'Bar');

        $this->assertSame('foo', $taxonomy->getType());
        $this->assertSame('bar', $taxonomy->getSlug());
        $this->assertSame('Bar', $taxonomy->getName());
    }

    public function testFactoryWithSortOrder(): void
    {
        $taxonomy = Taxonomy::factory('foo', 'Bår', 'Pømpidöm', 1000);

        $this->assertSame('foo', $taxonomy->getType());
        $this->assertSame('baar', $taxonomy->getSlug());
        $this->assertSame('Pømpidöm', $taxonomy->getName());
        $this->assertSame(1000, $taxonomy->getSortorder());
    }

    public function testFactoryWithMinimalParameters(): void
    {
        $taxonomy = Taxonomy::factory('foo', 'Døøp');

        $this->assertSame('foo', $taxonomy->getType());
        $this->assertSame('doeoep', $taxonomy->getSlug());
        $this->assertSame('Døøp', $taxonomy->getName());
        $this->assertSame(0, $taxonomy->getSortorder());
    }

    public function testSetSlug(): void
    {
        $taxonomy = Taxonomy::factory('foo', 'bar', 'baz');

        $taxonomy->setSlug('Qüx');

        $this->assertSame('quex', $taxonomy->getSlug());
    }

    public function testSetName(): void
    {
        $taxonomy = Taxonomy::factory('foo', 'bar', 'baz');

        $taxonomy->setName('Føø');

        $this->assertSame('Føø', $taxonomy->getName());

        $taxonomy->setName('bar');

        $this->assertSame('bar', $taxonomy->getName());
    }

    public function testSetSortorder(): void
    {
        $taxonomy = Taxonomy::factory('foo', 'bar', 'baz', 1000);

        $taxonomy->setSortorder(10);

        $this->assertSame(10, $taxonomy->getSortorder());
    }
}
