<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Configuration\Config;
use Bolt\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class CopyThemesCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'bolt:copy-themes';

    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $publicDirectory;

    /** @var Config */
    private $config;

    public function __construct(Filesystem $filesystem, string $publicFolder, string $projectDir, Config $config)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->publicDirectory = $projectDir . '/' . $publicFolder;
        $this->config = $config;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Copy theme files into the public/themes folder');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $publicDir = $this->getPublicDirectory();

        $io = new SymfonyStyle($input, $output);

        // Determine if we can use ../themes or not.
        if (file_exists(dirname(dirname(dirname(__DIR__))) . '/themes')) {
            $baseDir = dirname(dirname(dirname(__DIR__))) . '/themes';
            $dirs = [
                $baseDir . '/base-2021' => $publicDir . '/theme/base-2021',
                $baseDir . '/base-2018' => $publicDir . '/theme/base-2018',
                $baseDir . '/skeleton' => $publicDir . '/theme/skeleton',
            ];
        } else {
            if (Version::installType() === 'Git clone') {
                $io->error('This command only works with the \'Composer install\' install type.');

                return 1;
            }
            $io->error('Run \'composer require bolt/themes\' before using this command.');

            return 1;
        }

        $io->newLine();
        $io->text('Installing Bolt themes as <info>hard copies</info>.');
        $io->newLine();

        $rows = [];
        $exitCode = 0;

        foreach ($dirs as $originDir => $targetDir) {
            $message = basename($targetDir);

            try {
                $this->filesystem->remove($targetDir);
                $this->hardCopy($originDir, $targetDir);

                $rows[] = [sprintf('<fg=green;options=bold>%s</>', "\xE2\x9C\x94"), $message, 'copied'];
            } catch (\Throwable $e) {
                $exitCode = 1;
                $rows[] = [sprintf('<fg=red;options=bold>%s</>', "\xE2\x9C\x98"), $message, $e->getMessage()];
            }
        }

        if ($rows) {
            $io->table(['', 'Theme', 'Method / Error'], $rows);
        }

        if ($exitCode !== 0) {
            $io->error('Some errors occurred while installing themes.');
        } else {
            $io->success($rows ? 'All themes were successfully installed.' : 'No themes were provided by any bundle.');
        }

        return $exitCode;
    }

    /**
     * Copies origin to target.
     */
    private function hardCopy(string $originDir, string $targetDir): void
    {
        $mode = $this->config->get('general/filepermissions/folders', 0775);
        $this->filesystem->mkdir($targetDir, $mode);

        // We use a custom iterator to ignore VCS files
        $this->filesystem->mirror($originDir, $targetDir, Finder::create()->ignoreDotFiles(false)->in($originDir));
    }

    private function getPublicDirectory(): string
    {
        return $this->publicDirectory;
    }
}
