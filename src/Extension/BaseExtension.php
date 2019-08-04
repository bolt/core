<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Event\Subscriber\ExtensionSubscriber;
use Bolt\Widgets;
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
    public function injectObjects(Widgets $widgets): void
    {
        $this->widgets = $widgets;
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
