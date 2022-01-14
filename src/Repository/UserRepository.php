<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    /** @var string[] */
    private $userColumns = ['id', 'displayName', 'username', 'roles', 'email', 'lastIp'];

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

    public function findUsers(string $like, string $orderBy = null)
    {
        $alias = 'user';
        $qb = $this->createQueryBuilder($alias);

        if ($like) {
            foreach ($this->userColumns as $col) {
                $qb
                    ->orWhere(
                        $qb->expr()->like(sprintf('%s.%s', $alias, $col), sprintf(':%s', $col))
                    )
                    ->setParameter($col, $like);
            }
        }
        $qb->orderBy($this->createSortBy($orderBy, $alias));

        return $qb->getQuery()->getResult();
    }

    public function getFirstAdminUser(): ?User
    {
        $qb = $this->createQueryBuilder('user');
        $qb
            ->andWhere(
                $qb->expr()->like('user.roles', ':admin')
            )
            ->setParameter('admin', '%ROLE_ADMIN%')
            ->setMaxResults(1);

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

    private function createSortBy($order, $alias): Expr\OrderBy
    {
        if (mb_strpos($order, '-') === 0) {
            $direction = 'DESC';
            $order = sprintf('%s.%s', $alias, mb_substr($order, 1));
        } else {
            $direction = 'ASC';
            $order = sprintf('%s.%s', $alias, $order);
        }
        return new Expr\OrderBy($order, $direction);
    }
}
