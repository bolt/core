<?php

declare(strict_types=1);

namespace Bolt\Tests\Configuration\Parser;

use Bolt\Configuration\Config;
use Bolt\Controller\Backend\ClearCacheController;
use Bolt\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TraceableAdapter;
use Symfony\Component\Stopwatch\Stopwatch;
use Tightenco\Collect\Support\Collection;

class ConfigTest extends TestCase
{
    public function testCanParse(): void
    {
        $projectDir = dirname(dirname(dirname(__DIR__)));
        $cache = new TraceableAdapter(new FilesystemAdapter());
        $clearCacheController = new ClearCacheController();
        $kernel = $this->createMock(Kernel::class);
        $config = new Config('', '', new Stopwatch(), $projectDir, $cache, 'public', $clearCacheController, $kernel);

        $this->assertInstanceOf(Config::class, $config);
    }

    public function testConfigGet(): void
    {
        $projectDir = dirname(dirname(dirname(__DIR__)));
        $cache = new TraceableAdapter(new FilesystemAdapter());
        $clearCacheController = new ClearCacheController();
        $kernel = $this->createMock(Kernel::class);
        $config = new Config('', '', new Stopwatch(), $projectDir, $cache, 'public', $clearCacheController, $kernel);

        $this->assertSame('Bolt Core Git Clone', $config->get('general/sitename'));
    }

    public function testConfigHas(): void
    {
        $projectDir = dirname(dirname(dirname(__DIR__)));
        $cache = new TraceableAdapter(new FilesystemAdapter());
        $clearCacheController = new ClearCacheController();
        $kernel = $this->createMock(Kernel::class);
        $config = new Config('', '', new Stopwatch(), $projectDir, $cache, 'public', $clearCacheController, $kernel);

        $this->assertTrue($config->has('general/payoff'));
        $this->assertFalse($config->has('general/payoffXXXXX'));
    }

    public function testConfigGetMediaTypes(): void
    {
        $projectDir = dirname(dirname(dirname(__DIR__)));
        $cache = new TraceableAdapter(new FilesystemAdapter());
        $clearCacheController = new ClearCacheController();
        $kernel = $this->createMock(Kernel::class);
        $config = new Config('', '', new Stopwatch(), $projectDir, $cache, 'public', $clearCacheController, $kernel);

        /** @var Collection $mediaTypes */
        $mediaTypes = $config->getMediaTypes();

        $this->assertCount(10, $mediaTypes);
        $this->assertTrue($mediaTypes->contains('png'));
        $this->assertFalse($mediaTypes->contains('docx'));
    }
}
