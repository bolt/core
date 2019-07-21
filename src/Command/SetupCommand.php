<?php

declare(strict_types=1);

namespace Bolt\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{
    protected static $defaultName = 'bolt:setup';

    protected function configure(): void
    {
        $this->setDescription('Run Bolt setup / installation commands');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $command = $this->getApplication()->find('doctrine:database:create');
        $commandInput = new ArrayInput(['-q' => true]);
        $command->run($commandInput, $output);

        $command = $this->getApplication()->find('doctrine:schema:create');
        $commandInput = new ArrayInput([]);
        $command->run($commandInput, $output);

        $command = $this->getApplication()->find('bolt:add-user');
        $commandInput = new ArrayInput(['--admin' => true]);
        $command->run($commandInput, $output);

        $command = $this->getApplication()->find('doctrine:fixtures:load');
        $commandInput = new ArrayInput(['--append' => true]);
        $command->run($commandInput, $output);
    }
}
