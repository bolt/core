<?php

declare(strict_types=1);

namespace Bolt\Controller;

use Bolt\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

trait UserTrait
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    protected function getUser(): ?User
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            return null;
        }

        $user = $token->getUser();

        if (is_object($user) === false) {
            // e.g. anonymous authentication
            return null;
        }

        if (! $user instanceof User) {
            throw new UnsupportedUserException();
        }

        return $user;
    }
}
