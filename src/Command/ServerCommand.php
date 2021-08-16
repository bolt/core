<?php

declare(strict_types=1);

namespace Bolt\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class ServerCommand extends Command
{
    public const SYMFONY_SERVER = 'Symfony Server';
    public const PHP_SERVER = 'Built-in PHP Server';

    /** @var string */
    protected $ip;

    /** @var int */
    protected $port;

    /** @var string */
    private $publicFolder;

    public function __construct(string $publicFolder)
    {
        $this->publicFolder = $publicFolder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('server')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Preferred HTTP port rather than auto-find (default is 8000-9000')
            ->addOption('symfony', null, InputOption::VALUE_NONE, 'Force using Symfony server')
            ->addOption('php', null, InputOption::VALUE_NONE, 'Force using built-in PHP server')
            ->addOption('stop', null, InputOption::VALUE_NONE, 'Stop the running webserver')
            ->setDescription("Runs built-in web-server, Symfony first, then tries PHP's")
            ->setHelp("Runs built-in web-server, Symfony first, then tries PHP's");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Bolt Web Server');

        // Ensure CLI colors are on
        ini_set('cli_server.color', 'on');

        // Options
        $force_symfony = $input->getOption('symfony');
        $force_php = $input->getOption('php');
        $stop = $input->getOption('stop');

        // Find PHP
        $executableFinder = new PhpExecutableFinder();
        $php = $executableFinder->find(false);

        $this->ip = '127.0.0.1';
        $this->port = (int) ($input->getOption('port') ?? 8000);

        // Get an open port
        while (! $this->portAvailable($this->ip, $this->port)) {
            $this->port++;
        }

        // Setup the commands
        if ($stop) {
            $symfony_cmd = ['symfony', 'server:stop', '--ansi'];
            $php_cmd = [$php, 'bin/console', 'server:stop', '--ansi'];
        } else {
            $symfony_cmd = ['symfony', 'server:start', '--ansi', '--port=' . $this->port, '-d'];
            $php_cmd = [$php, 'bin/console', 'server:start', '--ansi', $this->port];
        }

        $commands = [
            self::SYMFONY_SERVER => $symfony_cmd,
            self::PHP_SERVER => $php_cmd,
        ];

        if ($force_symfony) {
            unset($commands[self::PHP_SERVER]);
        } elseif ($force_php) {
            unset($commands[self::SYMFONY_SERVER]);
        }

        $error = 1;

        foreach ($commands as $name => $command) {
            if ($this->runProcess($name, $command, $io)) {
                $error = 0;

                break;
            }
        }

        if ($error) {
            $io->error('Could not start either Symfony or PHP\'s built-in webserver');
        }

        return $error;
    }

    /**
     * @return Process
     */
    protected function runProcess(string $name, array $cmd, SymfonyStyle $io): ?Process
    {
        $process = new Process($cmd);
        $process->setTimeout(0);
        $process->start();

        $process->wait(function ($type, $buffer) use ($io): void {
            if (! mb_strpos($buffer, 'symfony: not found')) {
                $io->write($buffer);
            }
        });

        if ($name === self::PHP_SERVER && ! mb_strpos($process->getErrorOutput(), 'already been started.')) {
            $io->success('Built-in PHP web server listening on http://' . $this->ip . ':' . $this->port . ' (PHP v' . PHP_VERSION . ')');
        }

        if ($name === self::SYMFONY_SERVER && mb_strpos($process->getErrorOutput(), 'symfony: not found')) {
            return null;
        }

        return $process;
    }

    /**
     * Simple function test the port
     */
    protected function portAvailable(string $ip, int $port): bool
    {
        $fp = @fsockopen($ip, $port, $errno, $errstr, 0.1);
        if (! $fp) {
            return true;
        }

        fclose($fp);

        return false;
    }
}
