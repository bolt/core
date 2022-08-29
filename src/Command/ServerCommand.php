<?php

declare(strict_types=1);

namespace Bolt\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Process\Process;

class ServerCommand extends Command
{
    /** @var string */
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('bolt:server')
            ->addOption('port', 'p', InputOption::VALUE_OPTIONAL, 'Preferred HTTP port rather than auto-find (default is 8000-9000')
            ->addOption('stop', null, InputOption::VALUE_NONE, 'Stop the running webserver')
            ->addOption('full-path', null, InputOption::VALUE_NONE, 'Use the full path to start the server')
            ->setDescription("Suggest a command to run a webserver. Symfony first, then PHP's")
            ->setHelp("Suggest a command to run a webserver. Symfony first, then PHP's");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $stop = $input->getOption('stop');
        $ip = '127.0.0.1';
        $port = (int) ($input->getOption('port') ?? 8000);

        // Get an open port
        while (! $this->portAvailable($ip, $port)) {
            $port++;
        }

        if ($stop) {
            $command = $this->stopCommand();
        } else {
            $command = $this->startCommand($input->getOption('full-path'));
        }

        $io->comment(sprintf('You can <options=bold>%s</> a webserver by running the following command:', ($stop ? 'stop' : 'start')));

        $io->text(sprintf($command, $port));

        return Command::SUCCESS;
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

    protected function startCommand(bool $fullPath): string
    {
        // If we're not running in the 'projectDir', or `--full-path` is passed, set the dir explicitly.
        if ($fullPath || (getcwd() !== $this->projectDir)) {
            $dir = sprintf(' --%s=%s', ($this->hasSymfonyCommand() ? 'dir' : 'docroot'), $this->projectDir);
        } else {
            $dir = '';
        }

        if ($this->hasSymfonyCommand()) {
            return 'symfony server:start -d --port=%s' . $dir;
        }

        return 'php bin/console server:start %s' . $dir;
    }

    protected function stopCommand(): string
    {
        if ($this->hasSymfonyCommand()) {
            return 'symfony server:stop';
        }

        return 'php bin/console server:stop';
    }

    protected function hasSymfonyCommand()
    {
        $process = new Process(['symfony', 'version']);
        $process->setTimeout(0);
        $process->start();

        return $process->wait(function (): void {
        }) === 0;
    }
}
