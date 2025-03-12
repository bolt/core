<?php

declare(strict_types=1);

namespace Bolt\Tests\Configuration\Parser;

use Bolt\Configuration\Parser\ThemeParser;
use Illuminate\Support\Collection;

class ThemeParserTest extends ParserTestBase
{
    public function testCanParse(): void
    {
        $generalParser = new ThemeParser($this->getProjectDir(), self::getBasePath());
        $config = $generalParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testLocalOverridesParse(): void
    {
        $generalParser = new ThemeParser($this->getProjectDir(), self::getBasePath());
        $config = $generalParser->parse();

        $this->assertSame('bar', $config['foo']);
        $this->assertSame(['is', 'going', 'on', 'here?'], $config['what']);
    }
}
