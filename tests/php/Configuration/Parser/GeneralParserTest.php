<?php

declare(strict_types=1);

namespace Bolt\Tests\Configuration\Parser;

use Bolt\Configuration\Parser\GeneralParser;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Yaml\Exception\ParseException;
use Illuminate\Support\Collection;

class GeneralParserTest extends ParserTestBase
{
    public function testCanParse(): void
    {
        $generalParser = new GeneralParser($this->getProjectDir());
        $config = $generalParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testIgnoreNonsensicalFileParse(): void
    {
        $file = self::getBasePath() . 'bogus.yaml';
        $generalParser = new GeneralParser($this->getProjectDir(), $file);
        $config = $generalParser->parse();

        $this->assertInstanceOf(Collection::class, $config);
    }

    public function testBreakOnInvalidFileParse(): void
    {
        $file = self::getBasePath() . 'broken.yaml';
        $generalParser = new GeneralParser($this->getProjectDir(), $file);

        $this->expectException(ParseException::class);

        $generalParser->parse();
    }

    public function testBreakOnMissingFileParse(): void
    {
        $generalParser = new GeneralParser($this->getProjectDir(), 'foo.yml');

        $this->expectException(FileLocatorFileNotFoundException::class);

        $generalParser->parse();
    }

    public function testHasConfig(): void
    {
        $generalParser = new GeneralParser($this->getProjectDir());
        $config = $generalParser->parse();

        // Something in the file
        $this->assertSame('The amazing payoff goes here', $config['payoff']);

        // Something inferred
        $this->assertFalse($config['enforce_ssl']);
    }

    public function testFilenames(): void
    {
        $file = self::getBasePath() . 'bogus.yaml';
        $generalParser = new GeneralParser($this->getProjectDir(), $file);
        $generalParser->parse();

        $this->assertCount(2, $generalParser->getParsedFilenames());

        $this->assertSame($file, $generalParser->getInitialFilename());
        $this->assertSame(self::getBasePath() . 'bogus_local.yaml', $generalParser->getFilenameLocalOverrides());
    }

    public function testLocalOverridesParse(): void
    {
        $file = self::getBasePath() . 'bogus.yaml';
        $generalParser = new GeneralParser($this->getProjectDir(), $file);
        $config = $generalParser->parse();

        $this->assertSame('OverBar', $config['foo']);
        $this->assertSame('pidom', $config['pom']);
    }
}
