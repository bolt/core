<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Event\Subscriber\ExtensionSubscriber;
use Composer\Package\PackageInterface;
use ComposerPackages\Types;

class ExtensionRegistry
{
    /** @var ExtensionInterface[] */
    protected $extensions = [];

    /** @var array */
    protected $extensionClasses = [];

    /**
     * @see ExtensionCompilerPass::process()
     */
    public function addCompilerPass(array $extensionClasses): void
    {
        $this->extensionClasses = array_merge($this->extensionClasses, $extensionClasses);
    }

    private function addComposerPackages(): void
    {
        $packages = Types::get('bolt-extension');

        /** @var PackageInterface $package */
        foreach ($packages as $package) {
            $extra = $package->getExtra();

            if (! array_key_exists('entrypoint', $extra)) {
                $message = sprintf("The extension \"%s\" has no 'extra/entrypoint' defined in its 'composer.json' file.", $package->getName());

                throw new \Exception($message);
            }

            if (! class_exists($extra['entrypoint'])) {
                $message = sprintf("The extension \"%s\" has its 'extra/entrypoint' set to \"%s\", but that class does not exist", $package->getName(), $extra['entrypoint']);

                throw new \Exception($message);
            }

            $this->extensionClasses[] = $extra['entrypoint'];
        }
    }

    private function getExtensionClasses(): array
    {
        return array_unique($this->extensionClasses);
    }

    /**
     * @return ExtensionInterface[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function getExtensionNames(): array
    {
        return array_keys($this->extensions);
    }

    public function getExtension(string $name): ?ExtensionInterface
    {
        if (isset($this->extensions[$name])) {
            return $this->extensions[$name];
        }

        foreach ($this->extensions as $key => $extension) {
            if (mb_strpos($key, $name) !== false) {
                return $extension;
            }
        }

        return null;
    }

    /**
     * Runs once, invoked from the ExtensionSubscriber, to bootstrap all
     * extensions by injecting the container and running their initialize method
     *
     * @see ExtensionSubscriber::onKernelResponse()
     */
    public function initializeAll(array $objects, bool $runCli = false): void
    {
        $this->addComposerPackages();

        foreach ($this->getExtensionClasses() as $extensionClass) {
            // If the container has a public entry then resolve from there
            $extension = $objects['container']->has($extensionClass)
                ? $objects['container']->get($extensionClass)
                : new $extensionClass();

            $extension->injectObjects($objects);

            if (! $runCli && method_exists($extension, 'initialize')) {
                // If we're not running on the CLI. Assumably in a browser…
                $extension->initialize();
            } elseif (method_exists($extension, 'initializeCli')) {
                // We're running on the CLI
                $extension->initializeCli();
            }

            $this->extensions[$extensionClass] = $extension;
        }
    }

    /**
     * This method calls the `getRoutes()` method for all registered extension,
     * and compiles an array of routes. This is used in
     * Bolt\Extension\RoutesLoader::load() to add all these routes to the
     * (cached) routing.
     * The reason why we're not iterating over `$this->extensions` is that when
     * this method is called, they are not instantiated yet.
     */
    public function getAllRoutes(): array
    {
        $routes = [];

        $this->addComposerPackages();

        foreach ($this->getExtensionClasses() as $extensionClass) {
            $extension = new $extensionClass();

            if (method_exists($extension, 'getRoutes')) {
                $extRoutes = $extension->getRoutes();
                $routes = array_merge($routes, $extRoutes);
            }
        }

        return $routes;
    }
}
