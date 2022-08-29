<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;
use Tightenco\Collect\Support\Collection;

abstract class BaseFixture extends Fixture
{
    /** @var array */
    private $referencesIndex = [];

    /** @var array */
    private $taxonomyIndex = [];

    /**
     * During unit-tests, the fixtures are ran multiple times. Flush the
     * in-memory index, to prevent stale links to missing references.
     */
    protected function flushReferencesIndex(): void
    {
        $this->referencesIndex = [];
    }

    protected function getRandomReference(string $entityName)
    {
        if (isset($this->referencesIndex[$entityName]) === false) {
            $this->referencesIndex[$entityName] = [];

            foreach (array_keys($this->referenceRepository->getReferences()) as $key) {
                if (mb_strpos($key, $entityName . '_') === 0) {
                    $this->referencesIndex[$entityName][] = $key;
                }
            }
        }
        if (empty($this->referencesIndex[$entityName])) {
            throw new \Exception(sprintf('Cannot find any references for Entity "%s"', $entityName));
        }
        $randomReferenceKey = array_rand($this->referencesIndex[$entityName], 1);

        return $this->getReference($this->referencesIndex[$entityName][$randomReferenceKey]);
    }

    protected function getRandomTaxonomies(string $type, int $amount): array
    {
        if (empty($this->taxonomyIndex)) {
            foreach (array_keys($this->referenceRepository->getReferences()) as $key) {
                if (mb_strpos($key, 'taxonomy_') === 0) {
                    $tuples = explode('_', $key);
                    $this->taxonomyIndex[$tuples[1]][] = $key;
                }
            }
        }

        if (empty($this->taxonomyIndex[$type])) {
            return [];
        }

        $taxonomies = [];

        foreach ((array) array_rand($this->taxonomyIndex[$type], $amount) as $key) {
            $taxonomies[] = $this->getReference($this->taxonomyIndex[$type][$key]);
        }

        return $taxonomies;
    }

    protected function getImagesIndex($path): Collection
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
}
