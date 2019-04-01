<?php

declare(strict_types=1);

namespace Bolt\Tests\Configuration\Parser;

use Bolt\Configuration\Parser\MenuParser;
use PHPUnit\Framework\TestCase;
use Tightenco\Collect\Support\Collection;

class MenuParserTest extends TestCase
{
    /** @var MenuParser */
    private $menuParser;

    protected function setUp(): void
    {
        $this->menuParser = new MenuParser();
    }

    public function testCanParse(): void
    {
        $config = $this->menuParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testHasMenu(): void
    {
        $config = $this->menuParser->parse();

        $this->assertCount(1, $config);

        $this->assertArrayHasKey('main', $config);
        $this->assertCount(4, $config['main']);

        $this->assertArrayNotHasKey('foo', $config);
    }
}
