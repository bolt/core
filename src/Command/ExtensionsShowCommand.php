<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Extension\ExtensionInterface;
use Bolt\Extension\ExtensionRegistry;
use ComposerPackages\Dependencies;
use ComposerPackages\Versions;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'extensions:show', description: 'Show details for an extension')]
class ExtensionsShowCommand extends Command
{
    private readonly Dependencies $dependenciesManager;

    public function __construct(
        private readonly ExtensionRegistry $extensionRegistry
    ) {
        $this->dependenciesManager = new Dependencies();

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Extension name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $io = new SymfonyStyle($input, $output);

        $extensionName = $input->getArgument('name');

        /** @var ExtensionInterface $extension */
        $extension = $this->extensionRegistry->getExtension($extensionName);

        if (! $extension instanceof ExtensionInterface) {
            $io->caution('No such extension.');

            return Command::FAILURE;
        }

        $dependencyNames = iterator_to_array($this->dependenciesManager->get($extension->getComposerPackage()->getName()));

        $dependencies = [];
        foreach ($dependencyNames as $dependency) {
            $extDependency['name'] = $dependency;
            $extDependency['version'] = Versions::get($dependency);
            $dependencies[] = $extDependency;
        }

        $io->text('Details for:');
        $io->table(['Class', 'Extension name'], [[$extension->getClass(), $extension->getName()]]);

        $io->text('Dependencies:');
        if (! empty($dependencies)) {
            $io->table(['Dependency', 'Version'], $dependencies);
        } else {
            $io->text('No known dependencies');
        }

        return Command::SUCCESS;
    }
}
