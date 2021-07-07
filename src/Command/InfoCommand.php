<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Version;
use ComposerPackages\Packages;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InfoCommand extends Command
{
    use ImageTrait;

    /** @var string */
    protected static $defaultName = 'bolt:info';

    /** @var \Bolt\Doctrine\Version */
    private $doctrineVersion;

    /** @var object */
    private $composer;

    /** @var SymfonyStyle */
    private $io;

    private $projectDir;

    public function __construct(\Bolt\Doctrine\Version $doctrineVersion, string $projectDir)
    {
        $this->doctrineVersion = $doctrineVersion;
        $this->projectDir = $projectDir;

        parent::__construct();
    }

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
            )
            ->addOption('tablesInitialised', null, InputOption::VALUE_NONE, 'If set, outputs whether the Database tables are initialised or not');
    }

    /**
     * This method is executed after initialize(). It usually contains the logic
     * to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // If we just need to see if tables exist, exit quickly.
        if ($input->getOption('tablesInitialised')) {
            return (int) ! $this->doctrineVersion->tableContentExists();
        }

        $this->io = new SymfonyStyle($input, $output);

        $this->outputImage($this->io);

        $message = sprintf('Bolt version: <comment>%s</comment>', Version::VERSION);

        $this->io->text([$message, '']);

        try {
            $platform = $this->doctrineVersion->getPlatform();
            $tableExists = $this->doctrineVersion->tableContentExists() ? '' : sprintf(' - <error>Tables not initialised</error>');
            $withJson = $this->doctrineVersion->hasJson() ? 'with JSON' : 'without JSON';
        } catch (\Throwable $e) {
            $platform = [
                'client_version' => '',
                'driver_name' => '<error>Unknown - no database connection</error>',
                'connection_status' => '',
                'server_version' => '',
            ];
            $tableExists = '';
            $withJson = '';
        }

        $connection = ! empty($platform['connection_status']) ? sprintf(' - <comment>%s</comment>', $platform['connection_status']) : '';

        $this->io->listing([
            sprintf('Install type: <info>%s</info>', Version::installType()),
            sprintf('Database: <info>%s %s</info>%s%s <info>(%s)</info>', $platform['driver_name'], $platform['server_version'], $connection, $tableExists, $withJson),
            sprintf('PHP version: <info>%s</info>', PHP_VERSION),
            sprintf('Symfony version: <info>%s</info>', Version::getSymfonyVersion()),
            sprintf('Operating System: <info>%s</info> - <comment>%s</comment>', php_uname('s'), php_uname('r')),
        ]);

        $this->warnOutdatedComposerJson();

        $this->io->text('');

        return 0;
    }

    private function warnOutdatedComposerJson(): void
    {
        try {
            Packages::get('bolt/core');
        } catch (\Throwable $e) {
            // bolt/core is not a dependency. Perhaps we're in bolt/core itself?
            return;
        }

        // We check for 4.1.999, because "4.2.0-beta.1" is considered lower than "4.2.0"
        if (Version::compare('4.1.999', '<=')) {
            $composerFilename = $this->projectDir . DIRECTORY_SEPARATOR . 'composer.json';
            $this->composer = json_decode(file_get_contents($composerFilename));
            $warnings = 0;

            $warnings += $this->checkComposerScript('pre-install-cmd', 'Bolt\\ComposerScripts\\ProjectEventHandler::preInstall');
            $warnings += $this->checkComposerScript('post-install-cmd', 'Bolt\\ComposerScripts\\ProjectEventHandler::postInstall');
            $warnings += $this->checkComposerScript('pre-update-cmd', 'Bolt\\ComposerScripts\\ProjectEventHandler::preUpdate');
            $warnings += $this->checkComposerScript('post-update-cmd', 'Bolt\\ComposerScripts\\ProjectEventHandler::postUpdate');
            $warnings += $this->checkComposerScript('post-create-project-cmd', 'Bolt\\ComposerScripts\\ProjectEventHandler::postCreateProject');
            $warnings += $this->checkComposerScript('pre-package-uninstall', 'Bolt\\ComposerScripts\\ProjectEventHandler::prePackageUninstall');

            if ($warnings) {
                $update = 'Check the update instructions at <href=https://github.com/bolt/core/discussions/2318>https://github.com/bolt/core/discussions/2318</>';
                $this->io->writeln($update);
            }
        }
    }

    private function checkComposerScript(string $key, string $value): int
    {
        if (property_exists($this->composer->scripts, $key) && $this->composer->scripts->{$key}[0] === $value) {
            return 0;
        }

        $this->io->warning('The \'' . $key . '\' script in composer.json is out of date.');

        return 1;
    }
}
