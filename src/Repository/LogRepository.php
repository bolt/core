<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Entity\Log;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/** @extends ServiceEntityRepository<Log> */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('log');
    }

    /**
     * @return Pagerfanta<Log>
     */
    public function findLatest(int $page = 1, int $amount = 6): Pagerfanta
    {
        $qb = $this->getQueryBuilder()
            ->orderBy('log.createdAt', 'DESC')
            ->setMaxResults($amount);

        return $this->createPaginator($qb->getQuery(), $page, $amount);
    }

    /**
     * @return Pagerfanta<Log>
     */
    private function createPaginator(Query $query, int $page, int $amountPerPage): Pagerfanta
    {
        $paginator = new Pagerfanta(new QueryAdapter($query, true, true));
        $paginator->setMaxPerPage($amountPerPage);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}
