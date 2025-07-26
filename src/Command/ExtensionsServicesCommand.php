<?php

declare(strict_types=1);

namespace Bolt\Command;

use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExtensionsServicesCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'extensions:services';

    public function __construct(
        private readonly ContainerInterface $container
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('List services available in Extensions');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $rows = [];

        $reflectedContainer = new ReflectionClass($this->container);
        $reflectionProperty = $reflectedContainer->getProperty('services');
        $reflectionProperty->setAccessible(true);

        $publicServices = $reflectionProperty->getValue($this->container);

        foreach ($publicServices as $id => $name) {
            $rows[] = [$id, $name::class];
        }

        $reflectionProperty = $reflectedContainer->getProperty('privates');
        $reflectionProperty->setAccessible(true);

        $privateServices = $reflectionProperty->getValue($this->container);

        $io->text('Publicly accessible Services <info>(' . count($rows) . ')</info>:');
        $io->table(['Service ID', 'Class name / Alias'], $rows);

        $rows = [];

        foreach ($privateServices as $id => $name) {
            $rows[] = [$id, $name::class];
        }

        $io->text('Private Services <info>(' . count($rows) . ')</info>:');
        $io->table(['Service ID', 'Class name / Alias'], $rows);

        return Command::SUCCESS;
    }
}
