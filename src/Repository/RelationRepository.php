<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Sortable\Entity\Repository\SortableRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method (Relation | null) find($id, $lockMode = null, $lockVersion=null)
 * @method (Relation | null) findOneBy(array $criteria, array $orderBy=null)
 * @method Relation[]    findAll()
 * @method Relation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelationRepository extends SortableRepository
{
    public function __construct(RegistryInterface $registry)
    {
        $manager = $registry->getManagerForClass(Relation::class);

        if ($manager instanceof EntityManager) {
            parent::__construct($manager, $manager->getClassMetadata(Relation::class));
        } else {
            throw new \RuntimeException();
        }
    }

    /**
     * @var Relation[]
     */
    public function findRelations(Content $from, ?string $name, bool $biDirectional = false, ?int $limit = null): array
    {
        $result = $this->buildRelationQuery($from, $name)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        if (empty($result) === true && $biDirectional === true) {
            $result = $this->buildRelationQuery($from, $name, true)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        }

        return $result;
    }

    public function findFirstRelation(Content $from, ?string $name, bool $biDirectional = false): ?Relation
    {
        $result = $this->buildRelationQuery($from, $name)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result === null && $biDirectional === true) {
            $result = $this->buildRelationQuery($from, $name, true)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }

        return $result;
    }

    private function buildRelationQuery(Content $from, ?string $name, bool $reversed = false): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r, cfrom, cto')
            ->join('r.fromContent', 'cfrom')
            ->join('r.toContent', 'cto')
            ->orderBy('r.sort', 'DESC');

        if ($name !== null) {
            $qb->andWhere('r.name = :name')
                ->setParameter('name', $name, \PDO::PARAM_STR);
        }

        if ($reversed === false) {
            $qb->andWhere('cfrom.id', $from->getId(), \PDO::PARAM_INT);
        } else {
            $qb->andWhere('cto.id', $from->getId(), \PDO::PARAM_INT);
        }

        return $qb;
    }
}
