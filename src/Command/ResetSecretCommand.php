<?php

declare(strict_types=1);

namespace Bolt\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Webimpress\SafeWriter\Exception\ExceptionInterface;
use Webimpress\SafeWriter\FileWriter;

class ResetSecretCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'bolt:reset-secret';

    /** @var string */
    private $projectDir;

    public function __construct(string $projectDir)
    {
        parent::__construct();

        $this->projectDir = $projectDir;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Reset the APP_SECRET for this Bolt site.')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command generates a new APP_SECRET in the .env file
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

        $filepath = $this->projectDir . '/.env';
        $content = file_get_contents($filepath);
        $newSecret = 'APP_SECRET=' . bin2hex(random_bytes(16));
        $matchSecret = '/APP_SECRET=(.*)/';

        $newContent = preg_replace($matchSecret, $newSecret, $content);

        try {
            FileWriter::writeFile($filepath, $newContent);
        } catch (ExceptionInterface $e) {
            $message = sprintf('Failed to replace APP_SECRET. %s', $e->getMessage());
            $io->error($message);
        }

        $io->success('Secret replaced successfully!');

        return Command::SUCCESS;
    }
}
