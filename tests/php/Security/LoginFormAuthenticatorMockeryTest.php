<?php

declare(strict_types=1);

namespace Bolt\Tests\Security;

use Bolt\Entity\User;
use Bolt\Repository\UserRepository;
use Bolt\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginFormAuthenticatorMockeryTest extends \PHPUnit\Framework\TestCase
{
    const TEST_TOKEN = ['csrf_token' => null, 'username' => null];

    public function tearDown() {
        \Mockery::close();
    }

    function test_get_login_url()
    {
        $router = \Mockery::mock(RouterInterface::class);
        $router->shouldReceive('generate')
            ->with('bolt_login')
            ->once()
            ->andReturn('test_route');

        $res = $this->getTestObj(null, $router, null, null)->start(\Mockery::mock(Request::class));
        $this->assertEquals('test_route', $res->getTargetUrl());
    }

    function test_get_user()
    {
        $userRepository = \Mockery::mock(UserRepository::class);
        $userRepository->shouldReceive('findOneBy')
            ->andReturn(\Mockery::mock(User::class));
        $csrfTokenManager = \Mockery::mock(CsrfTokenManagerInterface::class);
        $csrfTokenManager->shouldReceive('isTokenValid')
            ->andReturn(true);

        $res = $this->getTestObj($userRepository, null, $csrfTokenManager, null)->getUser(self::TEST_TOKEN, \Mockery::mock(UserProviderInterface::class));
        $this->assertInstanceOf(User::class, $res);
    }

    function test_get_user_throws()
    {
        $csrfTokenManager = \Mockery::mock(CsrfTokenManagerInterface::class);
        $csrfTokenManager->shouldReceive('isTokenValid')
            ->andReturn(false);

        $this->expectException(InvalidCsrfTokenException::class);
        $this->getTestObj(null, null, $csrfTokenManager, null)->getUser(self::TEST_TOKEN, \Mockery::mock(UserProviderInterface::class));
    }
    
    private function getTestObj(?UserRepository $userRepository, ?RouterInterface $router, ?CsrfTokenManagerInterface $csrfTokenManager, ?UserPasswordEncoderInterface $userPasswordEncoder): LoginFormAuthenticator
    {
        return new LoginFormAuthenticator(
            $userRepository ?? \Mockery::mock(UserRepository::class),
            $router ?? \Mockery::mock(RouterInterface::class), 
            $csrfTokenManager ?? \Mockery::mock(CsrfTokenManagerInterface::class),
            $userPasswordEncoder ?? \Mockery::mock(UserPasswordEncoderInterface::class)
        );
    }
}