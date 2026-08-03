<?php

declare(strict_types=1);

namespace Bolt\Tests\Controller\Backend\Async;

use Bolt\Configuration\Config;
use Bolt\Controller\Backend\Async\UploadController;
use Bolt\Factory\MediaFactory;
use Bolt\Twig\TextExtension;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UploadControllerTest extends TestCase
{
    /**
     * A URL whose host resolves to a non-public address must be rejected
     * before the file is fetched. This test fails if the UrlSafetyChecker
     * guard is removed from the controller.
     */
    #[DataProvider('provideUnsafeUrls')]
    public function testHandleUrlUploadRejectsUnsafeUrl(string $url): void
    {
        $controller = $this->createController(new MockHttpClient());
        $controller->setCsrfTokenManager($this->validCsrfTokenManager());

        // The URL passes the format/scheme constraint, so the SSRF guard is the
        // only thing that can reject these hosts.
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        $request = Request::create('/async/upload-url', 'POST', [
            'url' => $url,
            'token' => 'valid',
        ]);

        $response = $controller->handleURLUpload($request, $validator);

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
        yield 'private range' => ['http://10.0.0.5/'];
        yield 'disallowed scheme' => ['ftp://example.com/file'];
    }

    /**
     * The submitted URL points at a public host and so passes UrlSafetyChecker,
     * but the connection itself lands on a private/link-local address (as would
     * happen with an HTTP redirect to the cloud metadata endpoint, or DNS
     * rebinding). The download must be aborted and no file kept.
     */
    public function testHandleUrlUploadBlocksConnectionToPrivateIp(): void
    {
        $cacheDir = sys_get_temp_dir() . '/bolt_upload_ssrf_' . bin2hex(random_bytes(4));

        // The transport reports the actually-connected IP as the AWS metadata
        // address, even though the requested host is a public IP literal.
        $mockClient = new MockHttpClient(
            new MockResponse('INTERNAL SECRET DATA', ['primary_ip' => '169.254.169.254'])
        );

        // A blocked download must never result in a persisted media record.
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('persist');
        $em->expects(self::never())->method('flush');

        $controller = $this->createController($mockClient, $em);
        $controller->setCsrfTokenManager($this->validCsrfTokenManager());
        $controller->setContainer($this->containerWithCacheDir($cacheDir));

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new ConstraintViolationList());

        $request = Request::create('/async/upload-url', 'POST', [
            'url' => 'http://93.184.216.34/image.png',
            'token' => 'valid',
        ]);

        try {
            $response = $controller->handleURLUpload($request, $validator);

            self::assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

            $payload = json_decode((string) $response->getContent(), true);
            self::assertStringContainsString('blocked', $payload['error']['message']);

            // The internal response body must not have been left on disk.
            self::assertSame([], glob($cacheDir . '/tmpupload/*') ?: []);
        } finally {
            (new Filesystem())->remove($cacheDir);
        }
    }

    private function createController(
        HttpClientInterface $httpClient,
        ?EntityManagerInterface $em = null
    ): UploadController {
        return new UploadController(
            $this->createMock(MediaFactory::class),
            $em ?? $this->createMock(EntityManagerInterface::class),
            $this->createMock(Config::class),
            $this->createMock(TextExtension::class),
            new Filesystem(),
            $this->createMock(TagAwareCacheInterface::class),
            $httpClient
        );
    }

    private function containerWithCacheDir(string $cacheDir): Container
    {
        $container = new Container(new ParameterBag(['kernel.cache_dir' => $cacheDir]));
        $container->set('parameter_bag', new ContainerBag($container));

        return $container;
    }

    private function validCsrfTokenManager(): CsrfTokenManagerInterface
    {
        $manager = $this->createMock(CsrfTokenManagerInterface::class);
        $manager->method('isTokenValid')->willReturn(true);

        return $manager;
    }
}
