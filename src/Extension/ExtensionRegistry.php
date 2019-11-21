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
    public function addCompilerPass(string $extensionClass): void
    {
        $this->extensionClasses[] = $extensionClass;
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
        return $this->extensionClasses;
    }

    /** @return ExtensionInterface[] */
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
            $extension = new $extensionClass();
            $extension->injectObjects($objects);

            if (! $runCli) {
                // If we're not running on the CLI. Assumably in a browser…
                $extension->initialize();
            } elseif (method_exists($extension, 'initializeCli')) {
                // We're running on the CLI
                $extension->initializeCli();
            }

            $this->extensions[$extensionClass] = $extension;
        }
    }
}
