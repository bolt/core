<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;

abstract class BaseFixture extends Fixture
{
    private $referencesIndex = [];
    private $taxonomyIndex = [];

    protected function getRandomReference(string $entityName)
    {
        if (isset($this->referencesIndex[$entityName]) === false) {
            $this->referencesIndex[$entityName] = [];

            foreach (array_keys($this->referenceRepository->getReferences()) as $key) {
                if (mb_strpos($key, $entityName.'_') === 0) {
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
