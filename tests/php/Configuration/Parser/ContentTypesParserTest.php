<?php

declare(strict_types=1);

namespace Bolt\Tests\Configuration\Parser;

use Bolt\Configuration\Parser\ContentTypesParser;
use Bolt\Configuration\Parser\GeneralParser;
use Bolt\Exception\ConfigurationException;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Yaml\Exception\ParseException;
use Tightenco\Collect\Support\Collection;

class ContentTypesParserTest extends ParserTestBase
{
    const NUMBER_OF_CONTENT_TYPES_IN_MINIMAL_FILE = 3;

    const AMOUNT_OF_ATTRIBUTES_IN_CONTENT_TYPE = 20;

    public function testCanParse(): void
    {
        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse());
        $config = $contentTypesParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testIgnoreNonsensicalFileParse(): void
    {
        $file = self::getBasePath() . 'bogus.yaml';
        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse(), $file);

        $this->expectException(ConfigurationException::class);

        $contentTypesParser->parse();
    }

    public function testBreakOnInvalidFileParse(): void
    {
        $file = self::getBasePath() . 'broken.yaml';
        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse(), $file);

        $this->expectException(ParseException::class);

        $contentTypesParser->parse();
    }

    public function testBreakOnMissingFileParse(): void
    {
        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse(), 'foo.yml');

        $this->expectException(FileLocatorFileNotFoundException::class);

        $contentTypesParser->parse();
    }

    public function testHasConfig(): void
    {
        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse());
        $config = $contentTypesParser->parse();

        $this->assertCount(6, $config);

        $this->assertArrayHasKey('homepage', $config);
        $this->assertCount(self::AMOUNT_OF_ATTRIBUTES_IN_CONTENT_TYPE, $config['homepage']);

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
        $this->assertSame('fa-home', $config['homepage']['icon_many']);
        $this->assertSame('fa-home', $config['homepage']['icon_one']);
        $this->assertFalse($config['homepage']['allow_numeric_slugs']);
    }

    public function testInferContentTypeValues(): void
    {
        $file = self::getBasePath() . 'minimal_content_types.yaml';

        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse(), $file);
        $config = $contentTypesParser->parse();

        $this->assertCount(self::NUMBER_OF_CONTENT_TYPES_IN_MINIMAL_FILE, $config);

        $this->assertArrayHasKey('foo', $config);
        $this->assertCount(self::AMOUNT_OF_ATTRIBUTES_IN_CONTENT_TYPE, $config['foo']);

        $this->assertSame('Bars', $config['foo']['name']);
        $this->assertSame('foo', $config['foo']['slug']);
        $this->assertSame('Bar', $config['foo']['singular_name']);
        $this->assertSame('bar', $config['foo']['singular_slug']);
        $this->assertTrue($config['foo']['show_on_dashboard']);
        $this->assertTrue($config['foo']['show_in_menu']);
        $this->assertFalse($config['foo']['sort']);
        $this->assertFalse($config['foo']['viewless']);
        $this->assertSame('fa-file', $config['foo']['icon_one']);
        $this->assertSame('fa-copy', $config['foo']['icon_many']);
        $this->assertFalse($config['foo']['allow_numeric_slugs']);
        $this->assertFalse($config['foo']['singleton']);

        $this->assertSame('published', $config['foo']['default_status']);
        $this->assertSame('bar', $config['foo']['singular_slug']);
        $this->assertSame('bar', $config['foo']['singular_slug']);
        $this->assertSame(6, $config['foo']['listing_records']);
        $this->assertSame(8, $config['foo']['records_per_page']);

        $this->assertIsIterable($config['foo']['fields']);
        $this->assertIsIterable($config['foo']['locales']);
        $this->assertIsIterable($config['foo']['groups']);
        $this->assertIsIterable($config['foo']['taxonomy']);
        $this->assertIsIterable($config['foo']['relations']);

        $this->assertArrayHasKey('qux', $config);
        $this->assertCount(self::AMOUNT_OF_ATTRIBUTES_IN_CONTENT_TYPE, $config['qux']);

        $this->assertSame('Corges', $config['qux']['name']);
        $this->assertSame('corges', $config['qux']['slug']);
        $this->assertSame('Corge', $config['qux']['singular_name']);
        $this->assertSame('corge', $config['qux']['singular_slug']);

        $this->assertArrayHasKey('grault', $config);
        $this->assertCount(self::AMOUNT_OF_ATTRIBUTES_IN_CONTENT_TYPE, $config['grault']);

        $this->assertSame('Grault', $config['grault']['name']);
        $this->assertSame('grault', $config['grault']['slug']);
        $this->assertSame('Waldo', $config['grault']['singular_name']);
        $this->assertSame('waldo', $config['grault']['singular_slug']);
    }
}
