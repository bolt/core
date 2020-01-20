<?php

declare(strict_types=1);

namespace Bolt\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetupCommand extends Command
{
    protected static $defaultName = 'bolt:setup';

    protected function configure(): void
    {
        $this
            ->setDescription('Run Bolt setup / installation commands')
            ->addOption('no-fixtures', 'nf', InputOption::VALUE_NONE, 'If set, no data fixtures will be created. An empty database wil be initialised.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;
        $io = new SymfonyStyle($input, $output);

        $command = $this->getApplication()->find('doctrine:database:create');
        $commandInput = new ArrayInput(['-q' => true]);
        $exitCode += $command->run($commandInput, $output);

        $command = $this->getApplication()->find('doctrine:schema:create');
        $commandInput = new ArrayInput([]);
        $exitCode += $command->run($commandInput, $output);

        $command = $this->getApplication()->find('bolt:add-user');
        $commandInput = new ArrayInput(['--admin' => true]);
        $exitCode += $command->run($commandInput, $output);

        $noFixtures = $input->getOption('no-fixtures');
        if (! $noFixtures) {
            $command = $this->getApplication()->find('doctrine:fixtures:load');
            $commandInput = new ArrayInput(['--append' => true]);
            $exitCode += $command->run($commandInput, $output);
        }

        $io->newLine();

        if ($exitCode !== 0) {
            $io->error('Some errors occurred while setting up Bolt.');
        } else {
            $io->success('Bolt was set up successfully! Start a web server, and open your Bolt site in a browser.');
        }

        return 0;
    }
}
