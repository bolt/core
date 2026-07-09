<?php

declare(strict_types=1);

namespace Bolt\Utils;

use RuntimeException;

/**
 * Guards against Server-Side Request Forgery (SSRF) for URLs that are fetched
 * server-side (e.g. "upload from URL"). It ensures the URL uses an allowed
 * scheme and that its host does not resolve to a private, reserved, loopback
 * or link-local address.
 */
class UrlSafetyChecker
{
    /**
     * @var string[]
     */
    private const ALLOWED_SCHEMES = ['http', 'https'];

    /**
     * @throws RuntimeException when the URL is not safe to fetch
     */
    public static function assertSafe(string $url): void
    {
        $parts = parse_url($url);

        if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            throw new RuntimeException('The provided URL is not valid.');
        }

        $scheme = mb_strtolower($parts['scheme']);
        if (! in_array($scheme, self::ALLOWED_SCHEMES, true)) {
            throw new RuntimeException(sprintf('The URL scheme "%s" is not allowed.', $scheme));
        }

        // Strip the brackets from IPv6 literals, e.g. `[::1]`.
        $host = mb_trim($parts['host'], '[]');

        $ips = self::resolveHost($host);

        if ($ips === []) {
            throw new RuntimeException('The host of the provided URL could not be resolved.');
        }

        foreach ($ips as $ip) {
            if (! self::isPublicIp($ip)) {
                throw new RuntimeException('The provided URL resolves to a non-public IP address and is not allowed.');
            }
        }
    }

    /**
     * @return string[]
     */
    private static function resolveHost(string $host): array
    {
        // If the host is already an IP literal, validate it directly.
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return [$host];
        }

        $ips = [];

        $records = @dns_get_record($host, DNS_A | DNS_AAAA);
        if (is_array($records)) {
            foreach ($records as $record) {
                if (! empty($record['ip'])) {
                    $ips[] = $record['ip'];
                } elseif (! empty($record['ipv6'])) {
                    $ips[] = $record['ipv6'];
                }
            }
        }

        // Fall back to an IPv4-only lookup if no records were returned.
        if ($ips === []) {
            $resolved = gethostbynamel($host);
            if (is_array($resolved)) {
                $ips = $resolved;
            }
        }

        return $ips;
    }

    private static function isPublicIp(string $ip): bool
    {
        // Unwrap IPv4-mapped IPv6 addresses (e.g. `::ffff:127.0.0.1`) so the
        // embedded IPv4 address is checked against the private/reserved ranges.
        if (mb_stripos($ip, '::ffff:') === 0) {
            $mapped = mb_substr($ip, 7);
            if (filter_var($mapped, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
                $ip = $mapped;
            }
        }

        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }
}
