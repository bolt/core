<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Utils\ThumbnailCacheClearer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearThumbnailCacheCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'cache:thumbs';

    /** @var ThumbnailCacheClearer */
    private $thumbnailCacheClearer;

    public function __construct(ThumbnailCacheClearer $thumbnailCacheClearer)
    {
        $this->thumbnailCacheClearer = $thumbnailCacheClearer;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Clear Bolt\'s thumbnail cache folder')
            ->setHelp(
                <<<'HELP'
The <info>%command.name%</info> command clears the `thumbs/` folder, that's used to efficiently store and serve thumbnail images.
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

        $io->text('// Clearing the Bolt thumbnail cache folder');

        $success = $this->thumbnailCacheClearer->run();

        if ($success) {
            $io->success('Thumbnail cache folder cleared successfully.');
        } else {
            $io->warning('Not all files in the Thumbnail cache could be cleared successfully. Remove them manually');
        }

        return Command::SUCCESS;
    }
}
