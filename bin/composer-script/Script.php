<?php

namespace Bolt\ComposerScripts;

use Composer\Script\Event;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;

class Script
{
    /** @var SymfonyStyle */
    protected static $console;

    protected static function init(string $message = '')
    {
        $consoleFactory = new SymfonyStyleFactory();
        self::$console = $consoleFactory->create();

        self::$console->note($message);
    }

    protected static function getProjectFolder(Event $event): string
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');

        return realpath($vendorDir . '/../');
    }

    /**
     * Execute a command in the CLI, as a separate process.
     */
    protected static function run(string $command): void
    {
        $process = new Process([$command]);
        $process->run();
    }
}
