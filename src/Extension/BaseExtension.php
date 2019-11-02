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
use Symfony\Component\HttpFoundation\Session\Session;
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
    /** @var Collection */
    private $config;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ObjectManager */
    private $objectManager;

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
        $path = $this->getBoltConfig()->getPath('extensions_config');

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
        $this->objectManager = $objects['manager'];
        $this->container = $objects['container'];
    }

    /**
     * Shortcut method to register a widget and inject the extension into it
     */
    public function registerWidget(WidgetInterface $widget): void
    {
        $widget->injectExtension($this);

        $widgets = $this->getWidgets();

        if ($widgets) {
            $widgets->registerWidget($widget);
        }
    }

    /**
     * Shortcut method to register a TwigExtension.
     */
    public function registerTwigExtension(TwigExtensionInterface $extension): void
    {
        if ($this->getTwig()->hasExtension(\get_class($extension))) {
            return;
        }

        $this->getTwig()->addExtension($extension);
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

    /**
     * This bit of code allows us to get services from the container, even if
     * they are not marked public. We need to be able to do this, because we
     * can't anticipate which services an extension's author will want to get,
     * and neither should we want to make them all public. So, we resort to
     * this, regardless of them being private / public. With great power comes
     * great responsibility.
     *
     * Note: We wouldn't have to do this, if we could Autowire services in our
     * own code. If you have good ideas on how to accomplish that, we'd be
     * happy to hear from your ideas.
     *
     * @throws \ReflectionException
     */
    public function getService(string $name)
    {
        $container = $this->getContainer();

        if ($container->has($name)) {
            return $container->get($name);
        }

        $reflectedContainer = new \ReflectionClass($container);
        $reflectionProperty = $reflectedContainer->getProperty('privates');
        $reflectionProperty->setAccessible(true);

        $privateServices = $reflectionProperty->getValue($container);

        if (array_key_exists($name, $privateServices)) {
            return $privateServices[$name];
        }

        return null;
    }

    public function getWidgets(): ?Widgets
    {
        return $this->getService(\Bolt\Widgets::class);
    }

    public function getBoltConfig(): Config
    {
        return $this->getService(\Bolt\Configuration\Config::class);
    }

    public function getTwig(): Environment
    {
        return $this->getService('twig');
    }

    public function getSession(): Session
    {
        return $this->getService('session');
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->getService('event_dispatcher');
    }

    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    public function getStopwatch(): Stopwatch
    {
        return $this->getService('debug.stopwatch');
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
