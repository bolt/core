<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\User;
use Bolt\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserExtension extends AbstractExtension
{
    /** @var UserRepository */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getuser', [$this, 'getUser']),
        ];
    }

    public function getUser($username = null, $id = null, $displayname = null, $email = null): ?User
    {
        $criteria = [];

        if ($id !== null) {
            $criteria['id'] = $id;
        }

        if ($username !== null) {
            $criteria['username'] = $username;
        }

        if ($displayname !== null) {
            $criteria['displayName'] = $displayname;
        }

        if ($email !== null) {
            $criteria['email'] = $email;
        }

        /** @var User|null $user */
        $user = $this->repository->findOneBy($criteria);

        if ($user instanceof User) {
            $user->setPassword('');
        }

        return $user;
    }
}
