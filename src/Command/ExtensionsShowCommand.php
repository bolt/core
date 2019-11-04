<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Extension\ExtensionRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ExtensionsShowCommand extends Command
{
    protected static $defaultName = 'extensions:show';

    /** @var ExtensionRegistry */
    private $extensionRegistry;

    private $extensionName;

    public function __construct(ExtensionRegistry $extensionRegistry, string $extensionName = null)
    {
        $this->extensionRegistry = $extensionRegistry;
        $this->extensionName = $extensionName;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show details for an extension')
            ->addArgument('name', $this->extensionName ? InputArgument::REQUIRED : InputArgument::OPTIONAL, 'Extension name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $extensions = $this->extensionRegistry->getExtensions();

        $io = new SymfonyStyle($input, $output);

        $io->text("Argument: " . $this->extensionName);

        /*
        $rows = [];

        foreach ($extensions as $extension) {
            $rows[] = [$extension->getClass(), $extension->getName()];
        }

        $io = new SymfonyStyle($input, $output);

        if (! empty($rows)) {
            $io->table(['Class', 'Extension name'], $rows);
        } else {
            $io->caution('No installed extensions could be found');
        }
        */

        return 0;
    }
}
