<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Configuration\Config;
use Bolt\Widgets;
use Composer\Package\PackageInterface;
use ComposerPackages\Types;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

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

    public function initializeAll(Widgets $widgets, Config $config, Environment $twig, EventDispatcherInterface $dispatcher): void
    {
        $this->addComposerPackages();

        foreach ($this->getExtensionClasses() as $extensionClass) {
            $extension = new $extensionClass();
            $extension->injectObjects($widgets, $config, $twig, $dispatcher);
            $extension->initialize();

            $this->extensions[$extensionClass] = $extension;
        }
    }
}
