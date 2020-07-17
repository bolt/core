<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Common\Str;
use Bolt\Extension\ExtensionRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Webmozart\PathUtil\Path;

class ExtensionsCopyConfigs extends Command
{
    protected static $defaultName = 'extensions:copy-configs';

    /** @var ExtensionRegistry */
    private $extensionRegistry;

    /** @var mixed */
    private $projectDir;

    public function __construct(ExtensionRegistry $extensionRegistry, ContainerInterface $container)
    {
        $this->extensionRegistry = $extensionRegistry;
        $this->projectDir = $container->getParameter('kernel.project_dir');

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Copy the config/config.yaml, config/services.yaml and config/routes.yaml files from extensions.')
            ->addOption('with-config', null, InputOption::VALUE_NONE, 'If set, Bolt will copy the default extension config.yaml file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $extensions = $this->extensionRegistry->getExtensions();

        $this->copyExtensionRoutesAndServices($extensions);

        if ($input->getOption('with-config')) {
            $this->copyExtensionConfig($extensions);
        }

        return 0;
    }

    public function copyExtensionConfig(array $packages): void
    {
        // @todo: Combine this with Bolt\Extension\ConfigTrait.php
        foreach ($packages as $package) {
            $path = $this->getPackagePath($package);

            $configPath = $this->getRelativePath($path) . '/config/config.yaml';
            if (file_exists($configPath)) {
                [$namespace, $name] = explode('\\', mb_strtolower($this->getNamespace($package)));
                $destination = $this->getExtensionConfigPath($namespace, $name);
                file_put_contents($destination, file_get_contents($configPath));
            }
        }
    }

    public function copyExtensionRoutesAndServices(array $packages): void
    {
        $oldExtensionsRoutes = glob($this->getExtensionRoutesPath());
        $oldExtensionsServices = glob($this->getExtensionServicesPath());

        foreach ($packages as $package) {
            $path = $this->getPackagePath($package);

            $extensionRoutesPath = $this->getRelativePath($path) . '/config/routes.yaml';
            if (file_exists($extensionRoutesPath)) {
                $destination = $this->getExtensionRoutesPath($path);
                $oldExtensionsRoutes = array_diff($oldExtensionsRoutes, [$destination]);
                file_put_contents($destination, file_get_contents($extensionRoutesPath));
            }

            $extensionServicesPath = $this->getRelativePath($path) . '/../config.services.yaml';
            if (file_exists($extensionServicesPath)) {
                $destination = $this->getExtensionServicesPath($path);
                $oldExtensionsServices = array_diff($oldExtensionsServices, [$destination]);
                file_put_contents($destination, file_get_contents($extensionRoutesPath));
            }
        }

        // Remove routes.yaml files for old (uninstalled) extensions
        array_map('unlink', $oldExtensionsRoutes);

        // Remove services.yaml files for old (uninstalled) extensions
        array_map('unlink', $oldExtensionsServices);
    }

    private function getRelativePath(string $path): string
    {
        return Path::makeRelative($path, $this->projectDir);
    }

    /**
     * Helper function that returns the path of the extension routes.yaml file
     * inside Bolt.
     */
    private function getExtensionRoutesPath(string $path = '*'): string
    {
        return $this->projectDir . '/config/routes/extension_' . Str::splitLast($path, '/') . '.yaml';
    }

    /**
     * Helper function that returns the path of the extension services.yaml file
     * inside Bolt.
     */
    private function getExtensionServicesPath(string $path = '*'): string
    {
        return $this->projectDir . '/config/packages/services_extension_' . Str::splitLast($path, '/') . '.yaml';
    }

    private function getExtensionConfigPath(string $namespace, string $name): string
    {
        return $this->projectDir . '/config/extensions/' . $namespace . '-' . $name . '.yaml';
    }

    private function getPackagePath($package): string
    {
        $reflection = new \ReflectionClass($package);

        return dirname(dirname($reflection->getFilename()));
    }

    private function getNamespace($package): string
    {
        $reflection = new \ReflectionClass($package);

        return $reflection->getNamespaceName();
    }
}
