<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;

abstract class BaseFixture extends Fixture
{
    private $referencesIndex = [];
    private $taxonomyIndex = [];

    protected function getRandomReference(string $className)
    {
        if (! isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];

            foreach (array_keys($this->referenceRepository->getReferences()) as $key) {
                if (mb_strpos($key, $className.'_') === 0) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }
        if (empty($this->referencesIndex[$className])) {
            throw new \Exception(sprintf('Cannot find any references for class "%s"', $className));
        }
        $randomReferenceKey = array_rand($this->referencesIndex[$className], 1);

        return $this->getReference($this->referencesIndex[$className][$randomReferenceKey]);
    }

    protected function getRandomTaxonomy(string $type)
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
            return null;
        }

        $randomReferenceKey = array_rand($this->taxonomyIndex[$type], 1);

        return $this->getReference($this->taxonomyIndex[$type][$randomReferenceKey]);
    }
}
