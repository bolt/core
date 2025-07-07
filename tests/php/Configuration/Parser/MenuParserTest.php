<?php

declare(strict_types=1);

namespace Bolt\Tests\Configuration\Parser;

use Bolt\Configuration\Parser\MenuParser;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Yaml\Exception\ParseException;
use Illuminate\Support\Collection;

class MenuParserTest extends ParserTestBase
{
    public function testCanParse(): void
    {
        $menuParser = new MenuParser($this->getProjectDir());
        $config = $menuParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testIgnoreNonsensicalFileParse(): void
    {
        $file = self::getBasePath() . 'bogus.yaml';
        $menuParser = new MenuParser($this->getProjectDir(), $file);
        $config = $menuParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testBreakOnInvalidFileParse(): void
    {
        $file = self::getBasePath() . 'broken.yaml';
        $menuParser = new MenuParser($this->getProjectDir(), $file);

        $this->expectException(ParseException::class);

        $menuParser->parse();
    }

    public function testBreakOnMissingFileParse(): void
    {
        $menuParser = new MenuParser($this->getProjectDir(), 'foo.yml');

        $this->expectException(FileLocatorFileNotFoundException::class);

        $menuParser->parse();
    }

    public function testHasMenu(): void
    {
        $menuParser = new MenuParser($this->getProjectDir());
        $config = $menuParser->parse();

        $this->assertCount(2, $config);

        $this->assertArrayHasKey('main', $config);
        $this->assertCount(4, $config['main']);

        $this->assertSame('Home', $config['main'][0]['label']);
        $this->assertSame('This is the <b>first<b> menu item.', $config['main'][0]['title']);
        $this->assertSame('homepage', $config['main'][0]['link']);
        $this->assertSame('homepage', $config['main'][0]['class']);
        $this->assertNull($config['main'][0]['submenu']);
        $this->assertSame('', $config['main'][0]['uri']);
        $this->assertArrayNotHasKey('foobar', $config['main'][0]);

        $this->assertCount(4, $config['main'][1]['submenu']);
        $this->assertSame('Sub 1', $config['main'][1]['submenu'][0]['label']);

        $this->assertArrayNotHasKey('foo', $config);
    }
}
