<?php

declare(strict_types=1);

namespace Bolt\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class WelcomeCommand extends Command
{
    use ImageTrait;

    /** @var string */
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

        $io->info('Welcome to your new Bolt project.');
        $io->text('For the full setup instructions, and other documentation, visit <href=https://docs.bolt.cm/installation/installation>https://docs.bolt.cm/installation/installation</>');
        $io->text('To ask questions and learn from our community, join our Slack channel: <href=https://slack.bolt.cm/>https://slack.bolt.cm/</>');
        $io->text('Additional resources and tips are available at <href=https://bolt.tips/>https://bolt.tips/</>');

        $io->note('If you wish to continue with SQLite, you can answer \'Y\' to the next question. If you\'d like to use MySQL or PostgreSQL, answer \'n\', configure `.env.local`, and then continue the setup.');

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Do yo want to continue the setup now? (Y/n)', true);

        $returnCode = Command::SUCCESS;

        if ($helper->ask($input, $output, $question)) {
            $command = $this->getApplication()->find('bolt:setup');
            $returnCode = $command->run(new ArrayInput([]), $output);
        } else {
            $io->info('To set up the database, run `bin/console bolt:setup` inside your new project folder.');
        }

        return $returnCode;
    }
}
