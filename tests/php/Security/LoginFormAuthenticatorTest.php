<?php

declare(strict_types=1);

namespace Bolt\Tests\Security;

use Bolt\Entity\User;
use Bolt\Repository\UserRepository;
use Bolt\Security\LoginFormAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class LoginFormAuthenticatorTest extends TestCase
{
    public const TEST_TOKEN = [
        'csrf_token' => null,
        'username' => 'test',
    ];

    public function testGetLoginUrl(): void
    {
        $router = $this->createMock(RouterInterface::class);
        $router->expects($this->once())
            ->method('generate')
            ->with('bolt_login')
            ->willReturn('test_route');

        $res = $this->getTestObj(null, $router, null, null)->start($this->createMock(Request::class));
        $this->assertSame('test_route', $res->getTargetUrl());
    }

    public function testGetUser(): void
    {
        $userRepository = $this->createConfiguredMock(UserRepository::class, [
            'findOneByUsername' => $this->createMock(User::class),
        ]);
        $csrfTokenManager = $this->createConfiguredMock(CsrfTokenManagerInterface::class, [
            'isTokenValid' => true,
        ]);

        $res = $this->getTestObj($userRepository, null, $csrfTokenManager, null)->getUser(self::TEST_TOKEN, $this->createMock(UserProviderInterface::class));
        $this->assertInstanceOf(User::class, $res);
    }

    public function testGetUserThrows(): void
    {
        $csrfTokenManager = $this->createConfiguredMock(CsrfTokenManagerInterface::class, [
            'isTokenValid' => false,
        ]);

        $this->expectException(InvalidCsrfTokenException::class);
        $this->getTestObj(null, null, $csrfTokenManager, null)->getUser(self::TEST_TOKEN, $this->createMock(UserProviderInterface::class));
    }

    private function getTestObj(?UserRepository $userRepository, ?RouterInterface $router, ?CsrfTokenManagerInterface $csrfTokenManager, ?UserPasswordEncoderInterface $userPasswordEncoder): LoginFormAuthenticator
    {
        return new LoginFormAuthenticator(
            $userRepository ?? $this->createMock(UserRepository::class),
            $router ?? $this->createMock(RouterInterface::class),
            $csrfTokenManager ?? $this->createMock(CsrfTokenManagerInterface::class),
            $userPasswordEncoder ?? $this->createMock(UserPasswordEncoderInterface::class)
        );
    }
}
