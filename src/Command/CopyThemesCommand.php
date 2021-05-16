<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Configuration\Config;
use Bolt\Extension\ExtensionRegistry;
use Bolt\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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

    /** @var ExtensionRegistry */
    private $extensionRegistry;

    /** @var string */
    private $projectDir;

    public function __construct(Filesystem $filesystem, string $publicFolder, string $projectDir, Config $config, ExtensionRegistry $extensionRegistry)
    {
        parent::__construct();

        $this->filesystem = $filesystem;
        $this->projectDir = $projectDir;
        $this->publicDirectory = $projectDir . '/' . $publicFolder;
        $this->config = $config;
        $this->extensionRegistry = $extensionRegistry;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Copy theme files into the public/themes folder')
            ->addArgument('theme', InputArgument::OPTIONAL, 'Specify the theme that needs to be copied.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Determine if we can use this command.
        if (Version::installType() === 'Git clone') {
            $io->error('This command only works with the \'Composer install\' install type.');

            return 1;
        }

        $themes = $this->getThemes($input);

        $dirs = $this->getDirs($themes);

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

    private function getThemes(InputInterface $input): array
    {
        $themes = collect($this->extensionRegistry->getThemes());

        // Is there a theme from the input?
        $theme = $input->hasArgument('theme') ? $input->getArgument('theme') : null;

        if ($theme) {
            $themes = $themes->filter(function ($package) use ($theme) {
                return mb_split('/', $package->getName())[1] === $theme;
            });
        }

        return $themes->toArray();
    }

    private function getBuiltinThemes(): array
    {
        // Determine if we can use ../themes or not.
        if (! file_exists(dirname(dirname(dirname(__DIR__))) . '/themes')) {
            return [];
        }

        $baseDir = dirname(dirname(dirname(__DIR__))) . '/themes';
        $publicDir = $this->getPublicDirectory();

        return [
            $baseDir . '/base-2021' => $publicDir . '/theme/base-2021',
            $baseDir . '/base-2018' => $publicDir . '/theme/base-2018',
            $baseDir . '/skeleton' => $publicDir . '/theme/skeleton',
        ];
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

    private function getDirs(array $themes): array
    {
        $dirs = [];
        $publicDir = $this->getPublicDirectory();

        foreach ($themes as $theme) {
            if ($theme->getName() === 'bolt/themes') {
                // Ignore the special case of bolt/themes, which is handled above.
                continue;
            }

            $source = $this->projectDir . '/vendor/' . $theme->getName();
            $target = $publicDir . '/theme/' . mb_split('/', $theme->getName())[1];
            $dirs[$source] = $target;
        }

        // Add the built-in theme dirs
        return array_merge($this->getBuiltinThemes(), $dirs);
    }
}
