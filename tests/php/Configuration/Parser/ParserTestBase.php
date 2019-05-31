<?php

declare(strict_types=1);

namespace Bolt\Tests\Configuration\Parser;

use PHPUnit\Framework\TestCase;

abstract class ParserTestBase extends TestCase
{
    public function getProjectDir()
    {
        return dirname(dirname(dirname(dirname(__DIR__))));
    }
    public static function getBasePath(): string
    {
        return dirname(dirname(dirname(__DIR__))) . '/fixtures/config/';
    }
}
