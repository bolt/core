<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Entity\User;
use Bolt\Enum\UserStatus;
use Bolt\Exception\DisabledUserLoginAttemptException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPostAuth(UserInterface $user): void
    {
        if (! $user instanceof User) {
            return;
        }

        if ($user->getStatus() !== UserStatus::ENABLED) {
            throw new DisabledUserLoginAttemptException();
        }
    }

    public function checkPreAuth(UserInterface $user): void
    {
    }
}
