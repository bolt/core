<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Configuration\Config;
use Bolt\Entity\Taxonomy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Taxonomy> */
class TaxonomyRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Config $config
    ) {
        parent::__construct($registry, Taxonomy::class);
    }

    public function factory(string $type, string $slug, ?string $name = null, int $sortorder = 0): Taxonomy
    {
        $taxonomy = $this->findOneBy([
            'type' => $type,
            'slug' => $slug,
        ]);

        if ($taxonomy) {
            return $taxonomy;
        }

        $taxonomy = new Taxonomy();

        if ($name === null) {
            $taxonomyDefinition = $this->config->getTaxonomy($type);
            if ($taxonomyDefinition === null) {
                $name = ucfirst($slug);
            } else {
                $name = $taxonomyDefinition->get('options')->get($slug, ucfirst($slug));
            }
        }

        $taxonomy->setType($type);
        $taxonomy->setSlug($slug);
        $taxonomy->setName($name);
        $taxonomy->setSortorder($sortorder);

        return $taxonomy;
    }
}
