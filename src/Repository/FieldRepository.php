<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Entity\Field;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method (Field | null) find($id, $lockMode = null, $lockVersion=null)
 * @method (Field | null) findOneBy(array $criteria, array $orderBy=null)
 * @method Field[]    findAll()
 * @method Field[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FieldRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Field::class);
    }

    private function getQueryBuilder(?QueryBuilder $qb = null)
    {
        return $qb ?: $this->createQueryBuilder('field');
    }

    public function findOneBySlug($slug): ?Field
    {
        return $this->getQueryBuilder()
            ->andWhere('field.value = :slug')
            ->setParameter('slug', \GuzzleHttp\json_encode([$slug]))
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return Field[] Returns an array of Field objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('field')
            ->andWhere('field.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('field.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
     */
    public function findOneBySomeField($value): ?Field
    {
        return $this->createQueryBuilder('field')
            ->andWhere('field.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
