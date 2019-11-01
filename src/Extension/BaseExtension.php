<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Configuration\Config;
use Bolt\Event\Subscriber\ExtensionSubscriber;
use Bolt\Widget\WidgetInterface;
use Bolt\Widgets;
use Cocur\Slugify\Slugify;
use Composer\Package\CompletePackage;
use Composer\Package\PackageInterface;
use ComposerPackages\Packages;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Yaml\Parser;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;
use Twig\Extension\ExtensionInterface as TwigExtensionInterface;

/**
 * BaseWidget can be used as easy starter pack or as a base for your own extensions.
 */
abstract class BaseExtension implements ExtensionInterface
{
    /** @var Widgets */
    private $widgets;

    /** @var Collection */
    private $config;

    /** @var Config */
    private $boltConfig;

    /** @var Environment */
    private $twig;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ObjectManager */
    private $objectManager;

    /** @var Stopwatch */
    private $stopwatch;

    /** @var ContainerInterface */
    private $container;

    /**
     * Returns the descriptive name of the Extension
     */
    public function getName(): string
    {
        return 'BaseExtension';
    }

    /**
     * Returns the classname of the Extension
     */
    public function getClass(): string
    {
        return static::class;
    }

    public function getConfig(): Collection
    {
        if ($this->config === null) {
            $this->initializeConfig();
        }

        return $this->config;
    }

    private function initializeConfig(): void
    {
        $config = [];

        $filenames = $this->getConfigFilenames();

        if (! is_readable($filenames['main']) && is_readable($this->getDefaultConfigFilename())) {
            $filesystem = new Filesystem();
            $filesystem->copy($this->getDefaultConfigFilename(), $filenames['main']);
        }

        $yamlParser = new Parser();
//dump($filenames);die();
        foreach ($filenames as $filename) {
            if (is_readable($filename)) {
                $config = array_merge($config, $yamlParser->parseFile($filename));
            }
        }

        $this->config = new Collection($config);
    }

    public function getConfigFilenames(): array
    {
        $slugify = new Slugify();
        $base = $slugify->slugify(str_replace('Extension', '', $this->getClass()));
        $path = $this->boltConfig->getPath('extensions_config');

        return [
            'main' => sprintf('%s%s%s.yaml', $path, DIRECTORY_SEPARATOR, $base),
            'local' => sprintf('%s%s%s_local.yaml', $path, DIRECTORY_SEPARATOR, $base),
        ];
    }

    public function hasConfigFilenames(): array
    {
        $result = [];

        foreach ($this->getConfigFilenames() as $filename) {
            if (is_readable($filename)) {
                $result[] = basename(dirname($filename)) . DIRECTORY_SEPARATOR . basename($filename);
            }
        }

        return $result;
    }

    private function getDefaultConfigFilename(): string
    {
        $reflection = new \ReflectionClass($this);

        return sprintf(
            '%s%s%s%s%s',
            dirname(dirname($reflection->getFilename())),
            DIRECTORY_SEPARATOR,
            'config',
            DIRECTORY_SEPARATOR,
            'config.yaml'
        );
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
     * Injects commonly used objects into the extension, for use by the
     * extension. Called from the listener.
     *
     * @see ExtensionSubscriber
     */
    public function injectObjects(array $objects): void
    {
        $this->widgets = $objects['widgets'];
        $this->boltConfig = $objects['config'];
        $this->twig = $objects['twig'];
        $this->eventDispatcher = $objects['dispatcher'];
        $this->objectManager = $objects['manager'];
        $this->stopwatch = $objects['stopwatch'];
        $this->container = $objects['container'];
    }

    /**
     * Shortcut method to register a widget and inject the extension into it
     */
    public function registerWidget(WidgetInterface $widget): void
    {
        $widget->injectExtension($this);

        $this->widgets->registerWidget($widget);
    }

    /**
     * Shortcut method to register a TwigExtension.
     */
    public function registerTwigExtension(TwigExtensionInterface $extension): void
    {
        if ($this->twig->hasExtension(\get_class($extension))) {
            return;
        }

        $this->twig->addExtension($extension);
    }

    public function registerListener($event, $callback): void
    {
        /** @var EventDispatcher $dp */
        $dp = $this->eventDispatcher;

        $dp->addListener($event, $callback);
    }

    /**
     * Get the ComposerPackage, that contains information about the package,
     * version, etc.
     */
    public function getComposerPackage(): ?CompletePackage
    {
        $className = $this->getClass();

        $finder = static function (PackageInterface $package) use ($className) {
            return array_key_exists('entrypoint', $package->getExtra()) && ($className === $package->getExtra()['entrypoint']);
        };
        $package = Packages::find($finder);

        return $package->current();
    }

    public function getWidgets(): Widgets
    {
        return $this->widgets;
    }

    public function getBoltConfig(): Config
    {
        return $this->boltConfig;
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    public function getStopwatch(): Stopwatch
    {
        return $this->stopwatch;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
