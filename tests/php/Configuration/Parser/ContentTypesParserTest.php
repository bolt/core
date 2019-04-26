<?php

declare(strict_types=1);

namespace Bolt\Tests\Configuration\Parser;

use Bolt\Configuration\Parser\ContentTypesParser;
use Bolt\Configuration\Parser\GeneralParser;
use Bolt\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Yaml\Exception\ParseException;
use Tightenco\Collect\Support\Collection;

class ContentTypesParserTest extends TestCase
{
    public static function getBasePath(): string
    {
        return dirname(dirname(dirname(__DIR__))) . '/fixtures/config/';
    }

    public function testCanParse(): void
    {
        $generalParser = new GeneralParser();
        $contentTypesParser = new ContentTypesParser($generalParser->parse());
        $config = $contentTypesParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testIgnoreNonsensicalFileParse(): void
    {
        $file = self::getBasePath() . 'bogus.yaml';
        $generalParser = new GeneralParser();
        $contentTypesParser = new ContentTypesParser($generalParser->parse(), $file);

        $this->expectException(ConfigurationException::class);

        $config = $contentTypesParser->parse();
    }

    public function testBreakOnInvalidFileParse(): void
    {
        $file = self::getBasePath() . 'broken.yaml';
        $generalParser = new GeneralParser();
        $contentTypesParser = new ContentTypesParser($generalParser->parse(), $file);

        $this->expectException(ParseException::class);

        $contentTypesParser->parse();
    }

    public function testBreakOnMissingFileParse(): void
    {
        $generalParser = new GeneralParser();
        $contentTypesParser = new ContentTypesParser($generalParser->parse(), 'foo.yml');

        $this->expectException(FileLocatorFileNotFoundException::class);

        $contentTypesParser->parse();
    }

    public function testHasConfig(): void
    {
        $generalParser = new GeneralParser();
        $contentTypesParser = new ContentTypesParser($generalParser->parse());
        $config = $contentTypesParser->parse();

        $this->assertCount(6, $config);

        $this->assertArrayHasKey('homepage', $config);
        $this->assertCount(21, $config['homepage']);

        $this->assertSame('Homepage', $config['homepage']['name']);
        $this->assertSame('Homepage', $config['homepage']['singular_name']);
        $this->assertCount(6, $config['homepage']['fields']);
        $this->assertCount(9, $config['homepage']['fields']['title']);
        $this->assertSame('Title', $config['homepage']['fields']['title']['label']);
        $this->assertSame('text', $config['homepage']['fields']['title']['type']);
        $this->assertTrue($config['homepage']['fields']['title']['localize']);
        $this->assertTrue($config['homepage']['viewless']);
        $this->assertTrue($config['homepage']['singleton']);
        $this->assertSame('published', $config['homepage']['default_status']);
        $this->assertSame('homepage', $config['homepage']['tablename']);
        $this->assertSame('fa-home', $config['homepage']['icon_many']);
        $this->assertSame('fa-home', $config['homepage']['icon_one']);
        $this->assertFalse($config['homepage']['allow_numeric_slugs']);
    }
}
