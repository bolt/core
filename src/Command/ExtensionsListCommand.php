<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Extension\ExtensionRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'extensions:list', description: 'List installed Extensions')]
class ExtensionsListCommand extends Command
{
    public function __construct(
        private readonly ExtensionRegistry $extensionRegistry
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $extensions = $this->extensionRegistry->getExtensions();

        $rows = [];

        foreach ($extensions as $extension) {
            $packageName = $extension->getComposerPackage() ? $extension->getComposerPackage()->getName() : 'No Package';
            $rows[] = [$packageName, $extension->getClass(), $extension->getName()];
        }

        $io = new SymfonyStyle($input, $output);

        if (! empty($rows)) {
            $io->text('Currently installed extensions:');
            $io->table(['Package name', 'Class', 'Extension name'], $rows);
        } else {
            $io->caution('No installed extensions could be found');
        }

        return Command::SUCCESS;
    }
}
