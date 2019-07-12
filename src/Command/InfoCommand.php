<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InfoCommand extends Command
{
    use ImageTrait;

    protected static $defaultName = 'bolt:info';

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Info about this Bolt Installation')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command shows some information about this installation of Bolt.
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

        $message = sprintf(
            'Bolt version: <info>%s</info>, <comment>%s (%s)</comment>.',
            Version::fullName(),
            Version::codeName(),
            Version::installType()
        );

        $io->text([
            $message,
            '',
        ]);

        return null;
    }
}
