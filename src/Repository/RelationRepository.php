<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Entity\Content;
use Bolt\Entity\Relation;
use Bolt\Enum\Statuses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Relation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relation[] findAll()
 * @method Relation[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relation::class);
    }

    /**
     * @var Relation[]
     */
    public function findRelations(Content $from, ?string $name, bool $biDirectional = false, ?int $limit = null, bool $publishedOnly = true): array
    {
        // Only get existing Relations from content that was persisted before
        if ($from->getId() === null) {
            return [];
        }

        $result = $this->buildRelationQuery($from, $name, false, $publishedOnly)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        if (empty($result) === true && $biDirectional === true) {
            $result = $this->buildRelationQuery($from, $name, true, $publishedOnly)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult();
        }

        return $result;
    }

    public function findFirstRelation(Content $from, ?string $name, bool $biDirectional = false, bool $publishedOnly = true): ?Relation
    {
        $result = $this->buildRelationQuery($from, $name, false, $publishedOnly)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($result === null && $biDirectional === true) {
            $result = $this->buildRelationQuery($from, $name, true, $publishedOnly)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }

        return $result;
    }

    private function buildRelationQuery(Content $from, ?string $name, bool $reversed = false, bool $publishedOnly = true): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r, cfrom, cto')
            ->join('r.fromContent', 'cfrom')
            ->join('r.toContent', 'cto')
            ->orderBy('r.position', 'DESC');

        if ($publishedOnly === true) {
            $qb->andWhere('cto.status = :status')
                ->setParameter('status', Statuses::PUBLISHED, \PDO::PARAM_STR);
        }

        if ($name !== null) {
            $qb->andWhere('r.name = :name')
                ->setParameter('name', $name, \PDO::PARAM_STR);
        }

        if ($reversed === false) {
            $qb->andWhere('r.fromContent = :from');
        } else {
            $qb->andWhere('r.toContent = :from');
        }
        $qb->setParameter(':from', $from);

        return $qb;
    }
}
