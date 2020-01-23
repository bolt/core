<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByUsername(string $username): ?User
    {
        $user = $this->findOneBy(['username' => $username]);
        return $user instanceof User ? $user : null;
    }

    public function findOneByCredentials(string $username): ?User
    {
        $qb = $this->createQueryBuilder('user');
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $qb->andWhere(
                $qb->expr()->eq(
                    $qb->expr()->lower('user.email'),
                    $qb->expr()->lower(':username')
                )
            );
        } else {
            $qb->andWhere(
                $qb->expr()->eq(
                    $qb->expr()->lower('user.username'),
                    $qb->expr()->lower(':username')
                )
            );
        }
        $qb->setParameter('username', $username);
        $user = $qb->getQuery()->getOneOrNullResult();
        return $user instanceof User ? $user : null;
    }

    public static function factory(string $displayName = '', string $username = '', string $email = ''): User
    {
        $user = new User();

        $user->setDisplayName($displayName);
        $user->setUsername($username);
        $user->setEmail($email);

        return $user;
    }
}
