<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Content\ContentType;
use Bolt\Entity\Content;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Content|null find($id, $lockMode = null, $lockVersion = null)
 * @method Content|null findOneBy(array $criteria, array $orderBy = null)
 * @method Content[]    findAll()
 * @method Content[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Content::class);
    }

    private function getQueryBuilder(QueryBuilder $qb = null)
    {
        return $qb ?: $this->createQueryBuilder('content');
    }

    public function findAll(int $page = 1, ContentType $contenttype = null): Pagerfanta
    {
        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a');

        if ($contenttype) {
            $qb->where('content.contentType = :ct')
                ->setParameter('ct', $contenttype['slug']);
        }

        return $this->createPaginator($qb->getQuery(), $page);
    }

    public function findLatest(): ?array
    {
        $qb = $this->getQueryBuilder()
            ->addSelect('a')
            ->innerJoin('content.author', 'a')
            ->where('content.publishedAt <= :now')
            ->orderBy('content.publishedAt', 'DESC')
            ->setParameter('now', new \DateTime());

        $result = $qb->getQuery()->getResult();

        return array_slice($result, 0, 6);
    }

    public function findOneBySlug(string $slug)
    {
        return $this->getQueryBuilder()
            ->innerJoin('Bolt\Entity\Field\SlugField', 'field')
            ->andWhere('field.value = :slug')
            ->setParameter('slug', json_encode([$slug]))
            ->getQuery()
            ->getResult()
            ;

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
