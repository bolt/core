<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Extension\ExtensionRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExtensionsListCommand extends Command
{
    protected static $defaultName = 'extensions:list';

    /** @var ExtensionRegistry */
    private $extensionRegistry;

    public function __construct(ExtensionRegistry $extensionRegistry)
    {
        $this->extensionRegistry = $extensionRegistry;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('List installed Extensions');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $extensions = $this->extensionRegistry->getExtensions();

        $rows = [];

        foreach ($extensions as $extension) {
            $rows[] = [$extension->getComposerPackage()->getName(), $extension->getClass(), $extension->getName()];
        }

        $io = new SymfonyStyle($input, $output);

        if (! empty($rows)) {
            $io->text('Currently installed extensions:');
            $io->table(['Package name', 'Class', 'Extension name'], $rows);
        } else {
            $io->caution('No installed extensions could be found');
        }

        return 0;
    }
}
