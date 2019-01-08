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
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginFormAuthenticatorProphecyTest extends \PHPUnit\Framework\TestCase
{
    const TEST_TOKEN = ['csrf_token' => null, 'username' => null];

    function test_get_login_url()
    {
        $router = $this->prophesize(RouterInterface::class);
        $router->generate(Argument::type('string'))->shouldBeCalledOnce()->willReturn('test_route');

        $res = $this->getTestObj(null, $router->reveal(), null, null)->start($this->prophesize(Request::class)->reveal());
        $this->assertEquals('test_route', $res->getTargetUrl());
    }

    function test_get_user()
    {
        $userRepository = $this->prophesize(UserRepository::class);
        $userRepository->findOneBy(['username' => null])->shouldBeCalledOnce()->wilLReturn($this->prophesize(User::class));
        $csrfTokenManager = $this->prophesize(CsrfTokenManagerInterface::class);
        $csrfTokenManager->isTokenValid(Argument::type(CsrfToken::class))->willReturn(true);

        $res = $this->getTestObj($userRepository->reveal(), null, $csrfTokenManager->reveal(), null)->getUser(self::TEST_TOKEN, $this->prophesize(UserProviderInterface::class)->reveal());
        $this->assertInstanceOf(User::class, $res);
    }

    function test_get_user_throws()
    {
        $csrfTokenManager = $this->prophesize(CsrfTokenManagerInterface::class);
        $csrfTokenManager->isTokenValid(Argument::any())->willReturn(false);

        $this->expectException(InvalidCsrfTokenException::class);
        $this->getTestObj(null, null, $csrfTokenManager->reveal(), null)->getUser(self::TEST_TOKEN, $this->prophesize(UserProviderInterface::class)->reveal());
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