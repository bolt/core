<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller\Backend\Async;

use Bolt\Controller\Backend\Async\EmbedController;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class EmbedControllerTest extends TestCase
{
    /**
     * A URL whose host resolves to a non-public address must be rejected
     * before any server-side request is made. This test fails if the
     * UrlSafetyChecker guard is removed from the controller.
     */
    #[DataProvider('provideUnsafeUrls')]
    public function testFetchEmbedRejectsUnsafeUrl(string $url): void
    {
        $controller = new EmbedController();
        $controller->setCsrfTokenManager($this->validCsrfTokenManager());

        $request = Request::create('/async/embed', 'POST', [
            'url' => $url,
            'token' => 'valid',
        ]);

        $response = $controller->fetchEmbed($request);

        self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $payload = json_decode((string) $response->getContent(), true);
        self::assertStringContainsString('not allowed', $payload['error']['message']);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideUnsafeUrls(): iterable
    {
        yield 'AWS metadata endpoint' => ['http://169.254.169.254/latest/meta-data/'];
        yield 'loopback' => ['http://127.0.0.1/'];
        yield 'private range' => ['http://192.168.1.1/'];
        yield 'disallowed scheme' => ['ftp://example.com/file'];
    }

    private function validCsrfTokenManager(): CsrfTokenManagerInterface
    {
        $manager = $this->createMock(CsrfTokenManagerInterface::class);
        $manager->method('isTokenValid')->willReturn(true);

        return $manager;
    }
}
