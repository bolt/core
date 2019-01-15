<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Content\ContentType;
use Bolt\Entity\Content;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method (Content | null) find($id, $lockMode = null, $lockVersion=null)
 * @method (Content | null) findOneBy(array $criteria, array $orderBy=null)
 * @method Content[]    findAll()
 * @method Content[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Content::class);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('content');
    }

    public function findForPage(int $page = 1, ?ContentType $contentType = null): Pagerfanta
    {
        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a');

        if ($contentType) {
            $qb->where('content.contentType = :ct')
                ->setParameter('ct', $contentType['slug']);
        }

        return $this->createPaginator($qb->getQuery(), $page);
    }

    public function findLatest(?ContentType $contentType = null, int $amount = 6): ?array
    {
        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a')
            ->orderBy('content.modifiedAt', 'DESC');

        if ($contentType) {
            $qb->where('content.contentType = :ct')
                ->setParameter('ct', $contentType['slug']);
        }

        $qb->setMaxResults($amount);
        return $qb->getQuery()->getResult();
    }

    public function findOneBySlug(string $slug): ?Content
    {
        return $this->getQueryBuilder()
            ->innerJoin(\Bolt\Entity\Field\SlugField::class, 'field')
            ->andWhere('field.value = :slug')
            ->setParameter('slug', json_encode([$slug]))
            ->getQuery()
            ->getOneOrNullResult();

//        ->join('m.PropertyEntity', 'p')
//        ->where('p.value IN (:values)')
//        ->setParameter('values',['red','yellow']);
    }

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Content::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function findAdjacentBy(string $column, string $direction, int $currentValue, ?string $contentType = null): ?Content
    {
        if ($direction === 'next') {
            $order = 'ASC';
            $whereClause = 'content.' . $column .' > :value';
        } else {
            $order = 'DESC';
            $whereClause = 'content.' . $column .' < :value';
        }

        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a')
            ->orderBy('content.' . $column, $order)
            ->where($whereClause)
            ->setParameter('value', $currentValue)
            ->setMaxResults(1);

        if ($contentType) {
            $qb->andWhere('content.contentType = :contentType')
                ->setParameter('contentType', $contentType);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

//    /**
//     * @return Content[] Returns an array of Content objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Content
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
