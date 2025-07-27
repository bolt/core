<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Entity\User;
use Bolt\Entity\UserAuthToken;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<UserAuthToken> */
class UserAuthTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAuthToken::class);
    }

    public static function factory(User $user, string $useragent, DateTime $validity): UserAuthToken
    {
        $userAuthToken = new UserAuthToken();

        $userAuthToken->setUser($user);
        $userAuthToken->setUseragent($useragent);
        $userAuthToken->setValidity($validity);

        return $userAuthToken;
    }

    // /**
    //  * @return UserAuthToken[] Returns an array of UserAuthToken objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserAuthToken
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
