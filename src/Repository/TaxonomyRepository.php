<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Configuration\Config;
use Bolt\Entity\Taxonomy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Taxonomy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Taxonomy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Taxonomy[] findAll()
 * @method Taxonomy[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxonomyRepository extends ServiceEntityRepository
{
    /** @var Config */
    private $config;

    public function __construct(ManagerRegistry $registry, Config $config)
    {
        parent::__construct($registry, Taxonomy::class);

        $this->config = $config;
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
