<?php

declare(strict_types=1);

namespace Bolt\ComposerScripts;

use Composer\Composer;
use Composer\Script\Event;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class Script
{
    /** @var SymfonyStyle */
    protected static $console;

    protected static function init(string $message = ''): void
    {
        self::$console = self::createSymfonyStyle();

        self::$console->note($message);
    }

    protected static function getProjectFolder(Event $event): string
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');

        return realpath($vendorDir . '/../');
    }

    /**
     * Execute a `bin/console` command in the CLI, as a separate process.
     */
    public static function runConsole(array $command): int
    {
        return self::runPHP(array_merge(['bin/console'], $command));
    }

    /**
     * Execute a PHP script in the CLI, as a separate process.
     *
     * Depending on the context, we're using either Symfony/Process 2.8.52
     * (bundled with composer up until 2.2.x) or Symfony/Process 5.4.x (if we're
     * using our own, or if the GLOBAL composer is 2.3.x and up). The signature
     * of the constructor changed
     * from: `public function __construct(string $commandline, …)`
     * to:   `public function __construct(array $command, …)`
     */
    public static function runPHP(array $command): int
    {
        // for windows systems add the interpreter 
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            array_unshift($command, 'php');
        }
        
        if (version_compare(Composer::getVersion(), '2.3-dev', '<')) {
            // Composer 2.2.x or lower
            /* @phpstan-ignore-next-line */
            $process = new Process(implode(' ', $command));
        } else {
            // Composer 2.3.0 or higher
            $process = new Process($command);
        }

        $process->setTty(self::isTtySupported());

        return $process->run();
    }

    /**
     * Create SymfonyStyle object. Taken from Symplify (which we might not
     * have at our disposal inside a 'project' installation)
     */
    public static function createSymfonyStyle(): SymfonyStyle
    {
        // to prevent missing argv indexes
        if (! isset($_SERVER['argv'])) {
            $_SERVER['argv'] = [];
        }

        $argvInput = new ArgvInput();
        $consoleOutput = new ConsoleOutput();

        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }

        return new SymfonyStyle($argvInput, $consoleOutput);
    }

    /**
     * Returns whether TTY is supported on the current operating system.
     */
    public static function isTtySupported(): bool
    {
        static $isTtySupported;

        if ($isTtySupported === null) {
            $isTtySupported = (bool) @proc_open('echo 1 >/dev/null', [['file', '/dev/tty', 'r'], ['file', '/dev/tty', 'w'], ['file', '/dev/tty', 'w']], $pipes);
        }

        return $isTtySupported;
    }
}
