<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Utils\ListFormatHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'cache:list-format-clear', description: 'Clear Bolt\'s cached ListFormat data. ')]
class ClearListFormatCommand extends Command
{
    public function __construct(
        private readonly ListFormatHelper $listFormatHelper
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command clears the `list_format` and `title` columns in the database. Be sure to run `bolt:update-list-format` afterwards.
HELP
            );
    }

    /**
     * This method is executed after initialize(). It usually contains the logic
     * to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->text('// Clearing the Bolt Content `list_format` and `title` columns.');

        $success = $this->listFormatHelper->clearColumns();

        if ($success) {
            $io->success('Cleared successfully. Be sure to run `bolt:update-list-format` next.');
        } else {
            $io->warning('Not all the rows could be cleared successfully. Remove them manually');
        }

        return Command::SUCCESS;
    }
}
