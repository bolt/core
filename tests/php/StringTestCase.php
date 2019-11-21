<?php

declare(strict_types=1);

namespace Bolt\Tests;

use PHPUnit\Framework\TestCase;

class StringTestCase extends TestCase
{
    public static function assertSameHtml(string $expected, string $actual): void
    {
        self::assertSame(
            preg_replace('/\s+/', '', $expected),
            preg_replace('/\s+/', '', $actual)
        );
    }
}
