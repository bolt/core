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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

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
        $controller = new UploadController(
            $this->createMock(MediaFactory::class),
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(Config::class),
            $this->createMock(TextExtension::class),
            $this->createMock(Filesystem::class),
            $this->createMock(TagAwareCacheInterface::class)
        );
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

    private function validCsrfTokenManager(): CsrfTokenManagerInterface
    {
        $manager = $this->createMock(CsrfTokenManagerInterface::class);
        $manager->method('isTokenValid')->willReturn(true);

        return $manager;
    }
}
