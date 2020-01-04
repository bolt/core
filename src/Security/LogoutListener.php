<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface
{
    private $em;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(ObjectManager $em, LoggerInterface $dbLogger)
    {
        $this->em = $em;
        $this->logger = $dbLogger;
    }

    public function logout(Request $request, Response $response, TokenInterface $token): void
    {
        $user = $token->getUser();
        if (! $user instanceof User) {
            return;
        }

        $userArr = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ];
        $this->logger->notice('User \'{username}\' logged out (manually)', $userArr);

        $this->em->remove($user->getUserAuthToken());
        $this->em->flush();
    }
}
