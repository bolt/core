<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Configuration\Config;
use Bolt\Extension\ExtensionRegistry;
use Bolt\Version;
use Composer\Package\PackageInterface;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Throwable;

class CopyThemesCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'bolt:copy-themes';

    private readonly string $publicDirectory;

    public function __construct(
        private readonly Filesystem $filesystem,
        string $publicFolder,
        private readonly string $projectDir,
        private readonly Config $config,
        private readonly ExtensionRegistry $extensionRegistry
    ) {
        parent::__construct();
        $this->publicDirectory = $this->projectDir . '/' . $publicFolder;
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

            return Command::FAILURE;
        }

        $themes = $this->getThemes($input);

        $dirs = $this->getDirs($themes);

        $io->newLine();
        $io->text('Installing Bolt themes as <info>hard copies</info>.');
        $io->newLine();

        $rows = [];
        $exitCode = Command::SUCCESS;

        foreach ($dirs as $originDir => $targetDir) {
            $message = basename($targetDir);

            try {
                $this->filesystem->remove($targetDir);
                $this->hardCopy($originDir, $targetDir);

                $rows[] = [sprintf('<fg=green;options=bold>%s</>', "\xE2\x9C\x94"), $message, 'copied'];
            } catch (Throwable $e) {
                $exitCode = Command::FAILURE;
                $rows[] = [sprintf('<fg=red;options=bold>%s</>', "\xE2\x9C\x98"), $message, $e->getMessage()];
            }
        }

        if ($rows) {
            $io->table(['', 'Theme', 'Method / Error'], $rows);
        }

        if ($exitCode !== Command::SUCCESS) {
            $io->error('Some errors occurred while installing themes.');
        } else {
            $io->success($rows ? 'All themes were successfully installed.' : 'No themes were provided by any bundle.');
        }

        return $exitCode;
    }

    private function getThemes(InputInterface $input): array
    {
        /** @var Collection<int, PackageInterface> $themes */
        $themes = collect($this->extensionRegistry->getThemes());

        // Is there a theme from the input?
        $theme = $input->hasArgument('theme') ? $input->getArgument('theme') : null;

        if ($theme) {
            $themes = $themes->filter(fn ($package): bool => mb_split('/', (string) $package->getName())[1] === $theme);
        }

        return $themes->toArray();
    }

    /**
     * @return array<string, string>
     */
    private function getBuiltinThemes(): array
    {
        // Determine if we can use ../themes or not.
        if (! file_exists(dirname(__DIR__, 3) . '/themes')) {
            return [];
        }

        $baseDir = dirname(__DIR__, 3) . '/themes';
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

    /**
     * @param PackageInterface[] $themes
     *
     * @return array<string, string>
     */
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
