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
    /** @var Security */
    private $security;

    /** @var UserRepository */
    private $repository;

    public function __construct(Security $security, UserRepository $repository)
    {
        $this->security = $security;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('isallowed', [$this, 'isAllowed']),
            new TwigFunction('getuser', [$this, 'getUser']),
            new TwigFunction('user', [$this, 'getUser']),
        ];
    }

    /**
     * @todo Replace with better method, once we've implemented https://github.com/bolt/core/issues/186
     */
    public function isAllowed(): bool
    {
        if ($this->security->getUser()) {
            return true;
        }

        return false;
    }

    public function getUser($username = null, $id = null, $displayname = null): ?User
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

        /** @var User|null $user */
        $user = $this->repository->findOneBy($criteria);

        if ($user instanceof User) {
            $user->setPassword('');
        }

        return $user;
    }
}
