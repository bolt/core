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

    public function findRelations(Content $from, ?string $name, ?int $limit = null, bool $publishedOnly = true, string $direction = "both"): array
    {
        // Only get existing Relations from content that was persisted before
        if ($from->getId() === null) {
            return [];
        }

        return $this->buildRelationQuery($from, $name, $publishedOnly, $direction)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findFirstRelation(Content $from, ?string $name, bool $publishedOnly = true): ?Relation
    {
        return $this->buildRelationQuery($from, $name, $publishedOnly)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function buildRelationQuery(Content $from, ?string $name, bool $publishedOnly = true, string $direction = "both"): QueryBuilder
    {
        $qb = $this->createQueryBuilder('r')
            ->select('r, cfrom, cto')
            ->join('r.fromContent', 'cfrom')
            ->join('r.toContent', 'cto')
            ->orderBy('r.position', 'ASC')
            ->setParameter(':from', $from);

        if ($publishedOnly === true) {
            $qb->andWhere('cto.status = :status')
                ->andWhere('cfrom.status = :status')
                ->setParameter('status', Statuses::PUBLISHED, \PDO::PARAM_STR);
        }

        if ($name === null) {
            switch (true) {
                case $direction === "both":
                    $qb->andWhere('r.fromContent = :from OR r.toContent = :from');
                    break;
                case $direction === "to":
                    $qb->andWhere('r.toContent = :from');
                    break;
                case $direction === "from":
                    $qb->andWhere('r.fromContent = :from');
                    break;
            }
        } else {
            switch (true) {
                case $direction === "both":
                    $qb->andWhere("(cfrom.contentType = :name AND r.toContent = :from) OR (cto.contentType = :name AND r.fromContent = :from)");
                    break;
                case $direction === "to":
                    $qb->andWhere("(cfrom.contentType = :name AND r.toContent = :from)");
                    break;
                case $direction === "from":
                    $qb->andWhere("(cto.contentType = :name AND r.fromContent = :from)");
                    break;
            }
            $qb->setParameter('name', $name, \PDO::PARAM_STR);
        }

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
