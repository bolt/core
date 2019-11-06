<?php

namespace Bolt\Security;


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

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        $user = $token->getUser();
        $this->em->remove($user->getUserAuthToken());
        $this->em->flush();
    }

}