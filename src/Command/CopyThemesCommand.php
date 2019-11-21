<?php

declare(strict_types=1);

namespace Bolt\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

class CopyThemesCommand extends Command
{
    protected static $defaultName = 'bolt:copy-themes';

    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Copy theme files into the public/themes folder');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Application $app */
        $app = $this->getApplication();
        /** @var KernelInterface $kernel */
        $kernel = $app->getKernel();

        $publicDir = $this->getPublicDirectory($kernel->getContainer());

        $io = new SymfonyStyle($input, $output);

        // Determine if we can use ../themes or not.
        if (file_exists(dirname(dirname(dirname(__DIR__))) . '/themes')) {
            $baseDir = dirname(dirname(dirname(__DIR__))) . '/themes';
            $dirs = [
                $baseDir . '/base-2018' => $publicDir .'/theme/base-2018',
                $baseDir . '/skeleton' => $publicDir .'/theme/skeleton',
            ];
        } else {
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
        $this->filesystem->mkdir($targetDir, 0777);

        // We use a custom iterator to ignore VCS files
        $this->filesystem->mirror($originDir, $targetDir, Finder::create()->ignoreDotFiles(false)->in($originDir));
    }

    private function getProjectDirectory(ContainerInterface $container): string
    {
        if ($container->hasParameter('kernel.project_dir')) {
            return $container->getParameter('kernel.project_dir');
        }

        return dirname(dirname(dirname(dirname(dirname(__DIR__)))));
    }

    private function getPublicDirectory(ContainerInterface $container): string
    {
        $defaultPublicDir = 'public';

        if (! $container->hasParameter('kernel.project_dir')) {
            return $defaultPublicDir;
        }

        $composerFilePath = $container->getParameter('kernel.project_dir').'/composer.json';

        if (! is_readable($composerFilePath)) {
            return $defaultPublicDir;
        }

        $composerConfig = json_decode(file_get_contents($composerFilePath), true);

        if (isset($composerConfig['extra']['public-dir'])) {
            return $composerConfig['extra']['public-dir'];
        }

        return $this->getProjectDirectory($container) . '/' . $defaultPublicDir;
    }
}
