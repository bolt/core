<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Version;
use ComposerPackages\Packages;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InfoCommand extends Command
{
    use ImageTrait;

    protected static $defaultName = 'bolt:info';

    /** @var \Bolt\Doctrine\Version */
    private $doctrineVersion;

    public function __construct(\Bolt\Doctrine\Version $doctrineVersion)
    {
        $this->doctrineVersion = $doctrineVersion;

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

        $message = sprintf('Bolt version: <comment>%s</comment>', Version::VERSION);

        $io->text([$message, '']);

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

        $io->listing([
            sprintf('Install type: <info>%s</info>', Version::installType()),
            sprintf('Database: <info>%s %s</info>%s%s <info>(%s)</info>', $platform['driver_name'], $platform['server_version'], $connection, $tableExists, $withJson),
            sprintf('PHP version: <info>%s</info>', PHP_VERSION),
            sprintf('Symfony version: <info>%s</info>', Version::getSymfonyVersion()),
            sprintf('Operating System: <info>%s</info> - <comment>%s</comment>', php_uname('s'), php_uname('r')),
        ]);

        $this->warnOutdatedComposerJson($io);

        $io->text('');

        return 0;
    }

    private function warnOutdatedComposerJson(SymfonyStyle $io): void
    {
        try {
            Packages::find('bolt/core');
        } catch (\Throwable $e) {
            // bolt/core is not a dependency. Perhaps we're in bolt/core itself?
            return;
        }

        if (Version::compare('4.2.0', '<=')) {
            $composer = json_decode(file_get_contents('composer.json'));
            $warning = false;

            if ($composer->scripts->{'post-install-cmd'}[0] !== 'php vendor/bolt/core/bin/composer-script/project-post-install-cmd.php') {
                $io->warning('The post-install-cmd script in composer.json is out of date.');
                $warning = true;
            }

            if ($composer->scripts->{'pre-update-cmd'}[0] ?? null !== 'php vendor/bolt/core/bin/composer-script/project-pre-update-cmd.php') {
                $io->warning('The pre-update-cmd script in composer.json is out of date.');
                $warning = true;
            }

            if ($composer->scripts->{'post-update-cmd'}[0] !== 'php vendor/bolt/core/bin/composer-script/project-post-update-cmd.php') {
                $io->warning('The post-update-cmd script in composer.json is out of date.');
                $warning = true;
            }

            if ($composer->scripts->{'post-create-project-cmd'}[0] !== 'php vendor/bolt/core/bin/composer-script/project-post-create-project-cmd.php') {
                $io->warning('The post-create-project-cmd script in composer.json is out of date.');
                $warning = true;
            }

            if ($warning) {
                $update = 'Check the update instructions at <href=https://github.com/bolt/core/discussions/2318>https://github.com/bolt/core/discussions/2318</>';
                $io->writeln($update);
            }
        }
    }
}
