<?php

declare(strict_types=1);

namespace Bolt\Tests\Security;

use Bolt\Entity\User;
use Bolt\Repository\UserRepository;
use Bolt\Security\LoginFormAuthenticator;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginFormAuthenticatorProphecyTest extends \PHPUnit\Framework\TestCase
{
    function test_get_login_url()
    {
        $router = $this->prophesize(RouterInterface::class);
        $router->generate(Argument::type('string'))->shouldBeCalledOnce()->willReturn('test_route');

        $res = $this->getTestObj(null, $router, null, null)->start($this->prophesize(Request::class));
        $this->assertEquals('test_route', $res->getTargetUrl());
    }

    function test_get_user()
    {
        $userRepository = $this->prophesize(UserRepository::class);
        $userRepository->findOneBy(['username' => 'test'])->shouldBeCalledOnce()->wilLReturn($this->prophesize(User::class));
        $csrfTokenManager = $this->prophesize(CsrfTokenManagerInterface::class);
        $csrfTokenManager->isTokenValid()->willReturn(true);

        $token = ['csrf_token' => null, 'username' => null];

        $res = $this->getTestObj($userRepository, null, $csrfTokenManager, null)->getUser($token, $this->prophesize(UserProviderInterface::class));
        $this->assertInstanceOf(User::class, $res);
    }

    function test_get_user_throws()
    {
        $csrfTokenManager = $this->prophesize(CsrfTokenManagerInterface::class);
        $csrfTokenManager->isTokenValid()->willReturn(false);

        $this->expectException(InvalidCsrfTokenException::class);
        $this->getTestObj(null, null, $csrfTokenManager, null)->getUser(['csrf_token' => null], $this->prophesize(UserProviderInterface::class));
    }
    
    private function getTestObj(?UserRepository $userRepository, ?RouterInterface $router, ?CsrfTokenManagerInterface $csrfTokenManager, ?UserPasswordEncoderInterface $userPasswordEncoder): LoginFormAuthenticator
    {
        return new LoginFormAuthenticator(
            $userRepository ?? $this->prophesize(UserRepository::class)->reveal(),
            $router ?? $this->prophesize(RouterInterface::class)->reveal(),
            $csrfTokenManager ?? $this->prophesize(CsrfTokenManagerInterface::class)->reveal(),
            $userPasswordEncoder ?? $this->prophesize(UserPasswordEncoderInterface::class)->reveal()
        );
    }
}