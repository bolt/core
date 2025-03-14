<?php

declare(strict_types=1);

namespace Bolt\Tests\Configuration\Parser;

use Bolt\Configuration\Parser\TaxonomyParser;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Yaml\Exception\ParseException;
use Illuminate\Support\Collection;

class TaxonomyParserTest extends ParserTestBase
{
    public function testCanParse(): void
    {
        $taxonomyParser = new TaxonomyParser($this->getProjectDir());
        $config = $taxonomyParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testBreakOnInvalidFileParse(): void
    {
        $file = self::getBasePath() . 'broken.yaml';
        $taxonomyParser = new TaxonomyParser($this->getProjectDir(), $file);

        $this->expectException(ParseException::class);

        $taxonomyParser->parse();
    }

    public function testBreakOnMissingFileParse(): void
    {
        $taxonomyParser = new TaxonomyParser($this->getProjectDir(), 'foo.yml');

        $this->expectException(FileLocatorFileNotFoundException::class);

        $taxonomyParser->parse();
    }

    public function testHasTaxonomies(): void
    {
        $taxonomyParser = new TaxonomyParser($this->getProjectDir());
        $config = $taxonomyParser->parse();

        $this->assertCount(3, $config);

        $this->assertArrayHasKey('tags', $config);
        $this->assertCount(13, $config['tags']);

        $this->assertSame('tags', $config['tags']['slug']);
        $this->assertSame('tag', $config['tags']['singular_slug']);
        $this->assertSame('tags', $config['tags']['behaves_like']);
        $this->assertSame('Add some freeform tags. Start a new tag by typing a comma or space.', $config['tags']['postfix']);
        $this->assertFalse($config['tags']['allow_spaces']);
        $this->assertSame('Tags', $config['tags']['name']);
        $this->assertSame('Tag', $config['tags']['singular_name']);
        $this->assertFalse($config['tags']['has_sortorder']);
        $this->assertFalse($config['tags']['required']);
        $this->assertTrue($config['tags']['multiple']);
        $this->assertEmpty($config['tags']['options']);
        $this->assertTrue($config['tags']['tagcloud']);

        $this->assertCount(8, $config['categories']['options']);

        $this->assertArrayNotHasKey('foobar', $config['tags']);
        $this->assertArrayNotHasKey('foo', $config);
    }

    public function testInferTaxonomyValues(): void
    {
        $file = self::getBasePath() . 'minimal_taxonomy.yaml';
        $taxonomyParser = new TaxonomyParser($this->getProjectDir(), $file);
        $config = $taxonomyParser->parse();

        $this->assertCount(2, $config);

        $this->assertArrayHasKey('foo', $config);
        $this->assertCount(13, $config['foo']);

        $this->assertSame('Bar', $config['foo']['name']);
        $this->assertSame('bar', $config['foo']['slug']);
        $this->assertSame('Bar', $config['foo']['singular_name']);
        $this->assertSame('bar', $config['foo']['singular_slug']);
        $this->assertFalse($config['foo']['has_sortorder']);
        $this->assertFalse($config['foo']['allow_spaces']);
        $this->assertSame('tags', $config['foo']['behaves_like']);
        $this->assertSame('', $config['foo']['prefix']);
        $this->assertSame('', $config['foo']['postfix']);
        $this->assertFalse($config['foo']['required']);
        $this->assertTrue($config['foo']['multiple']);
        $this->assertEmpty($config['foo']['options']);
        $this->assertTrue($config['foo']['tagcloud']);

        $this->assertCount(13, $config['qux']);

        $this->assertSame('Corge', $config['qux']['name']);
        $this->assertSame('corge', $config['qux']['slug']);
        $this->assertSame('Corge', $config['qux']['singular_name']);
        $this->assertSame('corge', $config['qux']['singular_slug']);
    }
}
