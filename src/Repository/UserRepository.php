<?php

declare(strict_types=1);

namespace Bolt\Repository;

use Bolt\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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
}
