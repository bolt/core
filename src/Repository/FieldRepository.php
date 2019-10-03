<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Common\Json;
use Bolt\Doctrine\Version;
use Bolt\Entity\Field;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Field|null find($id, $lockMode = null, $lockVersion = null)
 * @method Field|null findOneBy(array $criteria, array $orderBy = null)
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

    public function findOneBySlug(string $slug): ?Field
    {
        $qb = $this->getQueryBuilder();

        // Because Mysql 5.6 and Sqlite handle values in JSON differently, we
        // need to adapt the query.
        if (Version::useJsonFunction($qb)) {
            $where = "JSON_EXTRACT(field.value, '$[0]')";
        } else {
            $where = 'field.value';
            $slug = Json::json_encode([$slug]);
        }

        return $qb
            ->andWhere($where . ' = :slug')
            ->setParameter('slug', $slug)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
