<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Configuration\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class CopyAssetsCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'bolt:copy-assets';

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
            ->setDescription('Copy built asset files into the project root');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $publicDir = $this->getPublicDirectory();

        // Determine if we can use ../assets or not.
        if (file_exists(dirname(dirname(dirname(__DIR__))) . '/assets')) {
            $baseDir = dirname(dirname(dirname(__DIR__))) . '/assets';
            $dirs = [
                $baseDir . '/assets' => $publicDir . '/assets/',
                // $baseDir . '/translations' => $projectDir . '/translations/',
            ];
        } else {
            $baseDir = dirname(dirname(__DIR__));
            $dirs = [
                $baseDir . '/public/assets' => $publicDir . '/assets/',
                // $baseDir . '/translations' => $projectDir . '/translations/',
            ];
        }

        $io = new SymfonyStyle($input, $output);
        $io->newLine();
        $io->text('Installing Bolt assets as <info>hard copies</info>.');
        $io->newLine();

        $rows = [];
        $exitCode = 0;

        foreach ($dirs as $originDir => $targetDir) {
            $message = basename($targetDir);

            try {
                $this->filesystem->remove($targetDir);
                $this->hardCopy($originDir, $targetDir);
                dump($originDir, $targetDir);
                $rows[] = [sprintf('<fg=green;options=bold>%s</>', "\xE2\x9C\x94"), $message, 'copied'];
            } catch (\Throwable $e) {
                $exitCode = 1;
                $rows[] = [sprintf('<fg=red;options=bold>%s</>', "\xE2\x9C\x98"), $message, $e->getMessage()];
            }
        }

        $io->table(['', 'Folder', 'Method / Error'], $rows);

        if ($exitCode !== 0) {
            $io->error('Some errors occurred while installing assets.');
        } else {
            $io->success('All assets were successfully installed.');
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
