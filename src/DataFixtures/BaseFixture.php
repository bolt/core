<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Entity\Taxonomy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Exception;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

abstract class BaseFixture extends Fixture
{
    /**
     * @template T of object
     * @param class-string<T> $entityClass
     *
     * @return T
     */
    protected function getRandomReference(string $entityClass, ?string $filter = null): object
    {
        /** @var string[] $references */
        $references = array_keys($this->getReferenceRepo()->getReferencesByClass()[$entityClass]);

        if ($filter) {
            $references = array_filter(
                $references,
                static fn (string $reference): bool => str_starts_with($reference, $filter),
            );
        }

        if (empty($references)) {
            throw new Exception(sprintf('Cannot find any references for entity "%s"', $entityClass));
        }

        return $this->getReferenceRepo()->getReference(
            $references[array_rand($references)],
            $entityClass
        );
    }

    /**
     * @return Taxonomy[]
     */
    protected function getRandomTaxonomies(string $type, int $amount): array
    {
        /** @var string[] $taxonomies */
        $taxonomies = array_keys($this->getReferenceRepo()->getReferencesByClass()[Taxonomy::class]);
        $taxonomies = array_filter(
            $taxonomies,
            static fn (string $taxonomy): bool => str_contains($taxonomy, "_{$type}_"),
        );

        if (empty($taxonomies)) {
            return [];
        }

        /** @var int[] $randomTaxonomies */
        $randomTaxonomies = (array) array_rand($taxonomies, $amount);

        return array_map(
            fn (int $key): object => $this->getReferenceRepo()->getReference($taxonomies[$key], Taxonomy::class),
            $randomTaxonomies,
        );
    }

    protected function getImagesIndex(string $path): Collection
    {
        $finder = $this->findFiles($path);

        $files = [];

        foreach ($finder as $file) {
            $files[$file->getFilename()] = $file;
        }

        return new Collection($files);
    }

    private function findFiles(string $base): Finder
    {
        $fullpath = Path::canonicalize($base);

        $glob = '*.{jpg,png,gif,jpeg,webp,avif}';

        $finder = new Finder();
        $finder->in($fullpath)->depth('< 3')->sortByName()->name($glob)->files();

        return $finder;
    }

    protected function getOption(string $name): bool
    {
        return in_array($name, $_SERVER['argv'], true);
    }

    private function getReferenceRepo(): ReferenceRepository
    {
        return $this->referenceRepository ?? throw new RuntimeException('Must not be null');
    }
}
