<?php

declare(strict_types=1);

namespace Bolt\Command;

use Bolt\Common\Str;
use Bolt\Extension\ExtensionRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\PathUtil\Path;

class ExtensionsConfigureCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'extensions:configure';

    /** @var ExtensionRegistry */
    private $extensionRegistry;

    /** @var string */
    private $projectDir;

    public function __construct(ExtensionRegistry $extensionRegistry, string $projectDir)
    {
        $this->extensionRegistry = $extensionRegistry;
        $this->projectDir = $projectDir;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Copy the config/config.yaml, config/services.yaml and config/routes.yaml files from extensions.')
            ->addOption('with-config', null, InputOption::VALUE_NONE, 'If set, Bolt will copy the default extension config.yaml file')
            ->addOption('remove-services', null, InputOption::VALUE_NONE, 'If set, Bolt will remove the extension\'s services and routes files');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $extensions = $this->extensionRegistry->getExtensions();

        if ($input->getOption('remove-services')) {
            $this->deleteExtensionRoutesAndServices($extensions);

            return 0;
        }

        $this->copyExtensionRoutesAndServices($extensions);

        if ($input->getOption('with-config')) {
            $this->copyExtensionConfig($extensions);
        }

        $this->runExtensionInstall($extensions);

        return 0;
    }

    private function copyExtensionConfig(array $packages): void
    {
        // @todo: Combine this with Bolt\Extension\ConfigTrait.php
        foreach ($packages as $package) {
            $path = $this->getPackagePath($package);
            $origin = $this->getRelativePath($path) . '/config/config.yaml';

            [$namespace, $name] = explode('\\', mb_strtolower($this->getNamespace($package) . '\\'));
            $destination = $this->getExtensionConfigPath($namespace, $name);

            if (file_exists($origin) && ! file_exists($destination)) {
                file_put_contents($destination, file_get_contents($origin));
            }
        }
    }

    private function deleteExtensionRoutesAndServices(array $packages): void
    {
        foreach ($packages as $package) {
            $path = $this->getPackagePath($package);
            $services = $this->getExtensionServicesPath($path);

            if (is_file($services)) {
                unlink($services);
            }

            $routes = $this->getExtensionRoutesPath($path);
            if (is_file($routes)) {
                unlink($routes);
            }
        }
    }

    private function copyExtensionRoutesAndServices(array $packages): void
    {
        $oldExtensionsRoutes = glob($this->getExtensionRoutesPath());
        $oldExtensionsServices = glob($this->getExtensionServicesPath());

        foreach ($packages as $package) {
            $path = $this->getPackagePath($package);

            $sourceRoutes = $this->getRelativePath($path) . '/config/routes.yaml';
            if (file_exists($sourceRoutes)) {
                $destination = $this->getExtensionRoutesPath($path);
                $oldExtensionsRoutes = array_diff($oldExtensionsRoutes, [$destination]);
                file_put_contents($destination, file_get_contents($sourceRoutes));
            }

            $sourceServices = $this->getRelativePath($path) . '/config/services.yaml';
            if (file_exists($sourceServices)) {
                $destination = $this->getExtensionServicesPath($path);
                $oldExtensionsServices = array_diff($oldExtensionsServices, [$destination]);
                file_put_contents($destination, file_get_contents($sourceServices));
            }
        }

        // Remove routes.yaml files for old (uninstalled) extensions
        array_map('unlink', $oldExtensionsRoutes);

        // Remove services.yaml files for old (uninstalled) extensions
        array_map('unlink', $oldExtensionsServices);
    }

    private function runExtensionInstall(array $packages): void
    {
        foreach ($packages as $package) {
            if (method_exists($package, 'install')) {
                $package->install();
            }
        }
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
        return $this->projectDir . '/config/routes/extension_' . Str::splitLast($path, DIRECTORY_SEPARATOR) . '.yaml';
    }

    /**
     * Helper function that returns the path of the extension services.yaml file
     * inside Bolt.
     */
    private function getExtensionServicesPath(string $path = '*'): string
    {
        return $this->projectDir . '/config/packages/extension_' . Str::splitLast($path, DIRECTORY_SEPARATOR) . '.yaml';
    }

    private function getExtensionConfigPath(string $namespace, string $name): string
    {
        return sprintf('%s/config/extensions/%s%s%s.yaml',
            $this->projectDir,
            $namespace,
            (! empty($name) ? '-' : ''),
            $name);
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
