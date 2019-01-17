<?php

declare(strict_types=1);

namespace Bolt\DataFixtures;

use Bolt\Configuration\Config;
use Bolt\Entity\Taxonomy;
use Bolt\Utils\Str;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TaxonomyFixtures extends Fixture implements DependentFixtureInterface
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config->get('taxonomies');
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadTaxonomies($manager);

        $manager->flush();
    }

    private function loadTaxonomies(ObjectManager $manager): void
    {
        $order = 1;

        foreach ($this->config as $taxonomyDefinition) {
            if (! empty($taxonomyDefinition['options'])) {
                $options = $taxonomyDefinition['options'];
                foreach ($options as $key => $value) {
                    $taxonomy = Taxonomy::factory(
                        $taxonomyDefinition['slug'],
                        $key,
                        $value,
                        $taxonomyDefinition['has_sortorder'] ? $order++ : 0
                    );

                    $manager->persist($taxonomy);
                    $reference = 'taxonomy_' . $taxonomyDefinition['slug'] . '_' . Str::slug($key);
                    $this->addReference($reference, $taxonomy);
                }
            }
        }
    }
}
