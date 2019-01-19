<?php

declare(strict_types=1);

namespace spec\Bolt\Security;

use Bolt\Entity\User;
use Bolt\Repository\UserRepository;
use Bolt\Security\LoginFormAuthenticator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @mixin LoginFormAuthenticator
 */
class LoginFormAuthenticatorSpec extends ObjectBehavior
{
    public const TEST_TOKEN = [
        'csrf_token' => null,
        'username' => 'test',
    ];

    public function let(UserRepository $userRepository, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $userPasswordEncoder): void
    {
        $this->beConstructedWith($userRepository, $router, $csrfTokenManager, $userPasswordEncoder);
    }

    public function it_gets_login_url(RouterInterface $router, Request $request): void
    {
        $router->generate(Argument::type('string'))->shouldBeCalledOnce()->willReturn('test_route');
        $res = $this->start($request);
        $res->getTargetUrl()->shouldBe('test_route');
    }

    public function it_gets_user(CsrfTokenManagerInterface $csrfTokenManager, UserProviderInterface $userProvider, UserRepository $userRepository, User $user): void
    {
        $userRepository->findOneByUsername(self::TEST_TOKEN['username'])->shouldBeCalledOnce()->wilLReturn($user);
        $csrfTokenManager->isTokenValid(Argument::type(CsrfToken::class))->willReturn(true);
        $this->getUser(self::TEST_TOKEN, $userProvider)->shouldBeAnInstanceOf(User::class);
    }

    public function it_throws_while_getting_user(CsrfTokenManagerInterface $csrfTokenManager, UserProviderInterface $userProvider): void
    {
        $csrfTokenManager->isTokenValid(Argument::any())->willReturn(false);

        $this->shouldThrow(InvalidCsrfTokenException::class)->during(
            'getUser',
            [
                self::TEST_TOKEN,
                $userProvider,
            ]
        );
    }
}
