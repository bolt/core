<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    public function logout(Request $request, Response $response, TokenInterface $token): void
    {
        $user = $token->getUser();
        if (! $user instanceof User) {
            return;
        }
        $this->em->remove($user->getUserAuthToken());
        $this->em->flush();
    }
}
