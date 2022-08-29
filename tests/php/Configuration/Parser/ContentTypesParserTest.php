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
    public const NUMBER_OF_CONTENT_TYPES_IN_MINIMAL_FILE = 2;

    public const AMOUNT_OF_ATTRIBUTES_IN_CONTENT_TYPE = 27;

    public const AMOUNT_OF_ATTRIBUTES_IN_FIELD = 32;

    public const ALLOWED_LOCALES = 'en|nl|es|fr|de|pl|it|hu|pt_BR|ja|nb|nn|nl_NL|nl_BE';

    public const DEFAULT_LOCALE = 'nl';

    public function testCanParse(): void
    {
        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse(), self::DEFAULT_LOCALE, self::ALLOWED_LOCALES);
        $config = $contentTypesParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testIgnoreNonsensicalFileParse(): void
    {
        $file = self::getBasePath() . 'bogus.yaml';
        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse(), self::DEFAULT_LOCALE, self::ALLOWED_LOCALES, $file);

        $this->expectException(ConfigurationException::class);

        $contentTypesParser->parse();
    }

    public function testBreakOnInvalidFileParse(): void
    {
        $file = self::getBasePath() . 'broken.yaml';
        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse(), self::DEFAULT_LOCALE, self::ALLOWED_LOCALES, $file);

        $this->expectException(ParseException::class);

        $contentTypesParser->parse();
    }

    public function testBreakOnMissingFileParse(): void
    {
        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse(), self::DEFAULT_LOCALE, self::ALLOWED_LOCALES, 'foo.yml');

        $this->expectException(FileLocatorFileNotFoundException::class);

        $contentTypesParser->parse();
    }

    public function testHasConfig(): void
    {
        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse(), self::DEFAULT_LOCALE, self::ALLOWED_LOCALES);
        $config = $contentTypesParser->parse();

        $this->assertCount(9, $config);

        $this->assertArrayHasKey('homepage', $config);
        $this->assertCount(self::AMOUNT_OF_ATTRIBUTES_IN_CONTENT_TYPE, $config['homepage']);

        $this->assertSame('Homepage', $config['homepage']['name']);
        $this->assertSame('Homepage', $config['homepage']['singular_name']);
        $this->assertCount(6, $config['homepage']['fields']);
        $this->assertCount(self::AMOUNT_OF_ATTRIBUTES_IN_FIELD, $config['homepage']['fields']['title']);
        $this->assertArrayNotHasKey('key', $config['homepage']['fields']['title']);
        $this->assertSame('Title', $config['homepage']['fields']['title']['label']);
        $this->assertSame('text', $config['homepage']['fields']['title']['type']);
        $this->assertTrue($config['homepage']['fields']['title']['localize']);
        $this->assertTrue($config['homepage']['singleton']);
        $this->assertSame('published', $config['homepage']['default_status']);
        $this->assertSame('fa-home', $config['homepage']['icon_many']);
        $this->assertSame('fa-home', $config['homepage']['icon_one']);
        $this->assertFalse($config['homepage']['allow_numeric_slugs']);
        $this->assertContains('nl', $config['homepage']['locales']);
        $this->assertContains('ja', $config['homepage']['locales']);
        $this->assertSame('nl', $config['homepage']['fields']['title']['default_locale']);
    }

    public function testBrokenContentTypeValues(): void
    {
        $file = self::getBasePath() . 'broken_content_types.yaml';

        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse(), self::DEFAULT_LOCALE, self::ALLOWED_LOCALES, $file);

        $this->expectException(ConfigurationException::class);
        $contentTypesParser->parse();
    }

    public function testInferContentTypeValues(): void
    {
        $file = self::getBasePath() . 'minimal_content_types.yaml';

        $generalParser = new GeneralParser($this->getProjectDir());
        $contentTypesParser = new ContentTypesParser($this->getProjectDir(), $generalParser->parse(), self::DEFAULT_LOCALE, self::ALLOWED_LOCALES, $file);

        $config = $contentTypesParser->parse();

        $this->assertCount(self::NUMBER_OF_CONTENT_TYPES_IN_MINIMAL_FILE, $config);

        $this->assertArrayHasKey('bars', $config);
        $this->assertCount(self::AMOUNT_OF_ATTRIBUTES_IN_CONTENT_TYPE, $config['bars']);

        $this->assertSame('Bars', $config['bars']['name']);
        $this->assertSame('bars', $config['bars']['slug']);
        $this->assertSame('Bar', $config['bars']['singular_name']);
        $this->assertSame('bar', $config['bars']['singular_slug']);
        $this->assertTrue($config['bars']['show_on_dashboard']);
        $this->assertTrue($config['bars']['show_in_menu']);
        $this->assertSame('-createdAt', $config['bars']['order']);
        $this->assertFalse($config['bars']['viewless']);
        $this->assertSame('fa-file', $config['bars']['icon_one']);
        $this->assertSame('fa-copy', $config['bars']['icon_many']);
        $this->assertFalse($config['bars']['allow_numeric_slugs']);
        $this->assertFalse($config['bars']['singleton']);

        $this->assertSame('published', $config['bars']['default_status']);
        $this->assertSame('bar', $config['bars']['singular_slug']);
        $this->assertSame('bar', $config['bars']['singular_slug']);
        $this->assertSame(6, $config['bars']['listing_records']);
        $this->assertSame(8, $config['bars']['records_per_page']);

        $this->assertIsIterable($config['bars']['fields']);
        $this->assertIsIterable($config['bars']['locales']);
        $this->assertIsIterable($config['bars']['groups']);
        $this->assertIsIterable($config['bars']['taxonomy']);
        $this->assertIsIterable($config['bars']['relations']);

        $this->assertArrayHasKey('corges', $config);
        $this->assertCount(self::AMOUNT_OF_ATTRIBUTES_IN_CONTENT_TYPE, $config['corges']);

        $this->assertSame('Corges', $config['corges']['name']);
        $this->assertSame('corges', $config['corges']['slug']);
        $this->assertSame('Corge', $config['corges']['singular_name']);
        $this->assertSame('corge', $config['corges']['singular_slug']);
    }
}
