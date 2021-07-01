<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Widget\WidgetInterface;
use Bolt\Widgets;
use Cocur\Slugify\Slugify;
use Composer\Package\CompletePackageInterface;
use Composer\Package\PackageInterface;
use ComposerPackages\Packages;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Twig\Environment;
use Twig\Extension\ExtensionInterface as TwigExtensionInterface;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

/**
 * BaseWidget can be used as easy starter pack or as a base for your own extensions.
 */
abstract class BaseExtension implements ExtensionInterface
{
    use ServicesTrait;
    use ConfigTrait;

    /** @var string */
    private $slug;

    /**
     * Returns the descriptive name of the Extension
     */
    public function getName(): string
    {
        return 'BaseExtension';
    }

    /**
     * Returns the slugified name of the Extension
     */
    public function getSlug(): string
    {
        if ($this->slug === null) {
            $this->slug = Slugify::create()->slugify($this->getName());
        }

        return $this->slug;
    }

    /**
     * Called when initialising the Extension. Use this to register widgets or
     * do other tasks after boot.
     */
    public function initialize(): void
    {
        // Nothing
    }

    /**
     * Called when initialising the Extension from the Command Line only
     */
    public function initializeCli(): void
    {
        // Nothing
    }

    /**
     * Called when installing the Extension or when running
     * `bin/console extensions:configure`. Use this to install assets for the
     * Extension or other tasks that need to be done once.
     */
    public function install(): void
    {
        // Nothing
    }

    /**
     * Shortcut method to register a widget and inject the extension into it
     */
    public function addWidget(WidgetInterface $widget): void
    {
        $widget->injectExtension($this);

        $widgets = $this->getWidgets();

        if ($widgets) {
            $widgets->registerWidget($widget);
        }
    }

    /**
     * @deprecated
     */
    public function addTwigExtension(TwigExtensionInterface $extension): void
    {
    }

    /**
     * Shortcut method to add a namespace to the current Twig Environment.
     */
    public function addTwigNamespace(string $namespace = '', string $foldername = ''): void
    {
        if (empty($namespace)) {
            $namespace = $this->getSlug();
        }

        if (empty($foldername)) {
            $foldername = $this->getTemplateFolder();
        }

        if (! realpath($foldername)) {
            return;
        }

        /** @var FilesystemLoader|ChainLoader $twigLoaders */
        $twigLoaders = $this->getTwig()->getLoader();

        $twigLoaders = $twigLoaders instanceof ChainLoader ?
            $twigLoaders->getLoaders() :
            [$twigLoaders];

        foreach ($twigLoaders as $twigLoader) {
            if ($twigLoader instanceof FilesystemLoader) {
                $twigLoader->prependPath($foldername, $namespace);
            }
        }
    }

    private function getTemplateFolder(): ?string
    {
        $reflection = new \ReflectionClass($this);

        $folder = dirname($reflection->getFilename()) . DIRECTORY_SEPARATOR . 'templates';
        if (realpath($folder)) {
            return realpath($folder);
        }

        $folder = dirname(dirname($reflection->getFilename())) . DIRECTORY_SEPARATOR . 'templates';
        if (realpath($folder)) {
            return realpath($folder);
        }

        return null;
    }

    public function addListener($event, $callback): void
    {
        /** @var EventDispatcher $dp */
        $dp = $this->getEventDispatcher();

        $dp->addListener($event, $callback);
    }

    /**
     * @deprecated
     */
    public function registerWidget(WidgetInterface $widget): void
    {
        $this->addWidget($widget);
    }

    /**
     * @deprecated
     */
    public function registerTwigExtension(TwigExtensionInterface $extension): void
    {
    }

    /**
     * @deprecated
     */
    public function registerListener($event, $callback): void
    {
        $this->addListener($event, $callback);
    }

    /**
     * Get the ComposerPackage, that contains information about the package,
     * version, etc.
     */
    public function getComposerPackage(): ?CompletePackageInterface
    {
        $className = $this->getClass();

        $finder = static function (PackageInterface $package) use ($className) {
            return array_key_exists('entrypoint', $package->getExtra()) && ($className === $package->getExtra()['entrypoint']);
        };
        $package = Packages::find($finder);

        return $package->current();
    }
}
