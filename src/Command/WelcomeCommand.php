<?php

declare(strict_types=1);

namespace Bolt\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WelcomeCommand extends Command
{
    use ImageTrait;

    protected static $defaultName = 'bolt:welcome';

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Welcome command with basic resources about Bolt.')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command shows some information and resources to get started with your Bolt project.
HELP
            );
    }

    /**
     * This method is executed after initialize(). It usually contains the logic
     * to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->outputImage($io);

        $io->info('Welcome to your new Bolt project. To set up the database, run `bin/console bolt:setup` inside your new project folder.');
        $io->text('For the full setup instructions, and other documentation, visit <href=https://docs.bolt.cm/installation/installation>https://docs.bolt.cm/installation/installation</>');
        $io->text('To ask questions and learn from our community, join our Slack channel: <href=https://slack.bolt.cm/>https://slack.bolt.cm/</>');
        $io->text('Additional resources and tips are available at <href=https://bolt.tips/>https://bolt.tips/</>');

        $io->info('Happy building!');

        return 0;
    }
}
