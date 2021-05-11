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

    public function findRelations(Content $from, ?string $name, ?int $limit = null, bool $publishedOnly = true): array
    {
        // Only get existing Relations from content that was persisted before
        if ($from->getId() === null) {
            return [];
        }

        $result = $this->buildRelationQuery($from, $name, $publishedOnly)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findFirstRelation(Content $from, ?string $name, bool $publishedOnly = true): ?Relation
    {
        $result = $this->buildRelationQuery($from, $name, $publishedOnly)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    private function buildRelationQuery(Content $from, ?string $name, bool $publishedOnly = true): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r, cfrom, cto')
            ->join('r.fromContent', 'cfrom')
            ->join('r.toContent', 'cto')
            ->orderBy('r.position', 'DESC');

        $qb->andWhere('r.fromContent = :from OR r.toContent = :from');

        if ($publishedOnly === true) {
            $qb->andWhere('cto.status = :status')
                ->andWhere('cfrom.status = :status')
                ->setParameter('status', Statuses::PUBLISHED, \PDO::PARAM_STR);
        }

        if ($name !== null) {
            $qb->andWhere('cto.contentType = :name OR cfrom.contentType = :name')
                ->setParameter('name', $name, \PDO::PARAM_STR);
        }

        $qb->setParameter(':from', $from);

        return $qb;
    }

    public function findRelation(Content $from, Content $to): ?Relation
    {
        return $this->createQueryBuilder('r')
            ->select('r, cfrom, cto')
            ->join('r.fromContent', 'cfrom')
            ->join('r.toContent', 'cto')
            ->orderBy('r.position', 'DESC')
            ->orWhere('r.fromContent = :from AND r.toContent = :to')
            ->orWhere('r.fromContent = :to AND r.toContent = :from')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
