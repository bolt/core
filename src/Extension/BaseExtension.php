<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Configuration\Config;
use Bolt\Event\Subscriber\ExtensionSubscriber;
use Bolt\Widgets;
use Cocur\Slugify\Slugify;
use Composer\Package\PackageInterface;
use ComposerPackages\Packages;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;
use Tightenco\Collect\Support\Collection;
use Twig\Extension\ExtensionInterface as TwigExtensionInterface;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * BaseWidget can be used as easy starter pack or as a base for your own extensions.
 */
abstract class BaseExtension implements ExtensionInterface, TwigExtensionInterface
{
    /** @var Widgets */
    protected $widgets;

    protected $config;

    /** @var Config */
    protected $boltConfig;
    
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

    public function getConfig()
    {
        if ($this->config === null) {
            $this->initializeConfig();
        }
        
        return $this->config;
    }

    private function initializeConfig()
    {
        $config = [];

        $filenames = $this->getConfigFilenames();

        if (!is_readable($filenames['main']) && is_readable($this->getDefaultConfigFilename())) {
            $filesystem = new Filesystem();
            $filesystem->copy($this->getDefaultConfigFilename(), $filenames['main']);
        }

        $yamlParser = new Parser();

        foreach($filenames as $filename) {
            if (is_readable($filename)) {
                $config = array_merge($config, $yamlParser->parseFile($filename));
            }
        }

        $this->config = new Collection($config);
    }

    private function getConfigFilenames()
    {
        $slugify = new Slugify();
        $base = $slugify->slugify(str_replace('Extension', '', $this->getClass()));
        $path = $this->boltConfig->getPath('extensions_config');

        $filenames = [
            'main' => sprintf('%s%s%s.yaml', $path, DIRECTORY_SEPARATOR, $base),
            'local' => sprintf('%s%s%s_local.yaml', $path, DIRECTORY_SEPARATOR, $base),
        ];

        return $filenames;
    }

    public function hasConfigFilenames(): array
    {
        $result = [];

        $filenames = $this->getConfigFilenames();

        foreach($this->getConfigFilenames() as $filename) {
            if (is_readable($filename)) {
                $result[] = basename(dirname($filename)) . DIRECTORY_SEPARATOR . basename($filename);
            }
        }

        return $result;
    }

    private function getDefaultConfigFilename()
    {
        $reflection = new \ReflectionClass($this);
        $filename = sprintf('%s%s%s%s%s',
            dirname(dirname($reflection->getFilename())) ,
            DIRECTORY_SEPARATOR,
            'config',
            DIRECTORY_SEPARATOR,
            'config.yaml'
        );

        return $filename;
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
     * extension. Called from the listener
     *
     * @see ExtensionSubscriber
     */
    public function injectObjects(Widgets $widgets, Config $boltConfig): void
    {
        $this->widgets = $widgets;
        $this->boltConfig = $boltConfig;
    }

    public function getComposerPackage()
    {
        $className = $this->getClass();

        $finder = static function (PackageInterface $package) use ($className) {
            return array_key_exists('entrypoint', $package->getExtra()) && ($className === $package->getExtra()['entrypoint']);
        };
        $package = Packages::find($finder);

        return $package->current();
    }

    /**
     * Twig: Returns the token parser instances to add to the existing list.
     *
     * @return TokenParserInterface[]
     */
    public function getTokenParsers(): array
    {
        return [];
    }

    /**
     * Twig: Returns the node visitor instances to add to the existing list.
     *
     * @return NodeVisitorInterface[]
     */
    public function getNodeVisitors(): array
    {
        return [];
    }

    /**
     * Twig: Returns a list of filters to add to the existing list.
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [];
    }

    /**
     * Twig: Returns a list of tests to add to the existing list.
     *
     * @return TwigTest[]
     */
    public function getTests(): array
    {
        return [];
    }

    /**
     * Twig: Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [];
    }

    /**
     * Twig: Returns a list of operators to add to the existing list.
     *
     * @return array<array> First array of unary operators, second array of binary operators
     */
    public function getOperators()
    {
        return [];
    }
}
