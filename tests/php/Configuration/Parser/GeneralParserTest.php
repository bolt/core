<?php

declare(strict_types=1);

namespace Bolt\Tests\Configuration\Parser;

use Bolt\Configuration\Parser\GeneralParser;
use Bolt\Configuration\Parser\GeneralParser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Yaml\Exception\ParseException;
use Tightenco\Collect\Support\Collection;

class GeneralParserTest extends TestCase
{
    public static function getBasePath(): string
    {
        return dirname(dirname(dirname(__DIR__))) . '/fixtures/config/';
    }

    public function testCanParse(): void
    {
        $generalParser = new GeneralParser();
        $config = $generalParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testIgnoreNonsensicalFileParse(): void
    {
        $file = self::getBasePath() . 'bogus.yaml';
        $generalParser = new GeneralParser($file);
        $config = $generalParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testBreakOnInvalidFileParse(): void
    {
        $file = self::getBasePath() . 'broken.yaml';
        $generalParser = new GeneralParser($file);

        $this->expectException(ParseException::class);

        $generalParser->parse();
    }

    public function testBreakOnMissingFileParse(): void
    {
        $generalParser = new GeneralParser('foo.yml');

        $this->expectException(FileLocatorFileNotFoundException::class);

        $generalParser->parse();
    }


    public function testHasMenu(): void
    {
        $generalParser = new GeneralParser();
        $config = $generalParser->parse();

        $this->assertCount(2, $config);

        $this->assertArrayHasKey('main', $config);
        $this->assertCount(4, $config['main']);

        $this->assertSame('Home', $config['main'][0]['label']);
        $this->assertSame('This is the <b>first<b> menu item.', $config['main'][0]['title']);
        $this->assertSame('homepage', $config['main'][0]['link']);
        $this->assertSame('homepage', $config['main'][0]['class']);
        $this->assertNull($config['main'][0]['submenu']);
        $this->assertSame('', $config['main'][0]['uri']);
        $this->assertFalse($config['main'][0]['current']);
        $this->assertArrayNotHasKey('foobar', $config['main'][0]);

        $this->assertCount(4, $config['main'][1]['submenu']);
        $this->assertSame('Sub 1', $config['main'][1]['submenu'][0]['label']);

        $this->assertArrayNotHasKey('foo', $config);
    }
}
