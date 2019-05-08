<?php

declare(strict_types=1);

namespace Bolt\Tests\Configuration\Parser;

use Bolt\Configuration\Config;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TraceableAdapter;
use Symfony\Component\Cache\Simple\Psr6Cache;
use Symfony\Component\Stopwatch\Stopwatch;
use Tightenco\Collect\Support\Collection;

class ConfigTest extends TestCase
{

    public function testCanParse(): void
    {
        $projectDir = dirname(__DIR__);
        $cache = new Psr6Cache(new TraceableAdapter(new FilesystemAdapter()));
        $config = new Config(new Stopwatch(), $projectDir, $cache);

        $this->assertInstanceOf(Config::class, $config);
    }

    public function testConfigGet()
    {
        $projectDir = dirname(__DIR__);
        $cache = new Psr6Cache(new TraceableAdapter(new FilesystemAdapter()));
        $config = new Config(new Stopwatch(), $projectDir, $cache);

        $this->assertSame('Bolt Four Website', $config->get('general/sitename'));
    }

    public function testConfigHas()
    {
        $projectDir = dirname(__DIR__);
        $cache = new Psr6Cache(new TraceableAdapter(new FilesystemAdapter()));
        $config = new Config(new Stopwatch(), $projectDir, $cache);

        $this->assertTrue($config->has('general/payoff'));
        $this->assertFalse($config->has('general/payoffXXXXX'));
    }

    public function testConfigGetMediaTypes()
    {
        $projectDir = dirname(__DIR__);
        $cache = new Psr6Cache(new TraceableAdapter(new FilesystemAdapter()));
        $config = new Config(new Stopwatch(), $projectDir, $cache);

        /** @var Collection $mediaTypes */
        $mediaTypes = $config->getMediaTypes();

        $this->assertCount(8, $mediaTypes);
        $this->assertTrue($mediaTypes->contains('png'));
        $this->assertFalse($mediaTypes->contains('docx'));
    }
}