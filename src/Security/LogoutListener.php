<?php

declare(strict_types=1);

namespace Bolt\Security;

use Bolt\Entity\User;
use Bolt\Entity\UserAuthToken;
use Bolt\Log\LoggerTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface
{
    use LoggerTrait;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function logout(Request $request, Response $response, TokenInterface $token): void
    {
        /** @var User $user */
        $user = $token->getUser();

        if (! $user instanceof User) {
            return;
        }

        $this->requestStack->getSession()->invalidate();

        $this->logger->notice('User \'{username}\' logged out (manually, auth_token: {token_id}, {ip})', [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'token_id' => $authTokenId = $this->requestStack->getSession()->get('user_auth_token_id'),
            'ip' => $request->getClientIp(),
        ]);
    }
}
