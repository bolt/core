<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Entity\User;
use Bolt\Entity\UserAuthToken;
use Bolt\Log\LoggerTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface
{
    use LoggerTrait;

    /** @var EntityManagerInterface */
    private $em;

    /** @var SessionInterface */
    private $session;

    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }

    public function logout(Request $request, Response $response, TokenInterface $token): void
    {
        /** @var User $user */
        $user = $token->getUser();
        $authTokenId = $this->session->get('user_auth_token_id');

        $authToken = $this->em->find(UserAuthToken::class, $authTokenId);
        $this->em->remove($authToken);

        $this->session->invalidate();

        $this->logger->notice('User \'{username}\' logged out (manually)', [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ]);
    }
}
