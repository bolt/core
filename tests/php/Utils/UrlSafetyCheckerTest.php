<?php

declare(strict_types=1);

namespace Bolt\Tests\Utils;

use Bolt\Utils\UrlSafetyChecker;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class UrlSafetyCheckerTest extends TestCase
{
    #[DataProvider('providePublicUrls')]
    public function testAllowsPublicUrls(string $url): void
    {
        $this->expectNotToPerformAssertions();

        UrlSafetyChecker::assertSafe($url);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function providePublicUrls(): iterable
    {
        yield 'public IPv4 literal' => ['http://8.8.8.8/image.png'];
        yield 'public IPv4 literal over https' => ['https://1.1.1.1/image.png'];
        yield 'public IPv6 literal' => ['http://[2606:4700:4700::1111]/image.png'];
    }

    #[DataProvider('provideBlockedUrls')]
    public function testBlocksUnsafeUrls(string $url): void
    {
        $this->expectException(RuntimeException::class);

        UrlSafetyChecker::assertSafe($url);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideBlockedUrls(): iterable
    {
        // Disallowed schemes (defense in depth on top of the Url constraint).
        yield 'file scheme' => ['file:///etc/passwd'];
        yield 'gopher scheme' => ['gopher://127.0.0.1:11211/'];
        yield 'ftp scheme' => ['ftp://example.com/file'];

        // Loopback.
        yield 'IPv4 loopback' => ['http://127.0.0.1/'];
        yield 'IPv6 loopback' => ['http://[::1]/'];

        // Cloud metadata endpoint / link-local.
        yield 'AWS metadata' => ['http://169.254.169.254/latest/meta-data/'];

        // Private ranges.
        yield 'private 10/8' => ['http://10.0.0.5/'];
        yield 'private 172.16/12' => ['http://172.16.0.1/'];
        yield 'private 192.168/16' => ['http://192.168.1.1/'];

        // IPv4-mapped IPv6 pointing at loopback.
        yield 'IPv4-mapped loopback' => ['http://[::ffff:127.0.0.1]/'];

        // Malformed input.
        yield 'no host' => ['http:///nohost'];
        yield 'not a url' => ['notaurl'];
    }

    public function testBlocksLocalhostHostname(): void
    {
        // `localhost` resolves to a loopback address and must be rejected.
        $this->expectException(RuntimeException::class);

        UrlSafetyChecker::assertSafe('http://localhost/');
    }
}
