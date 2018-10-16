<?php declare(strict_types=1);

namespace Bolt\Twig\Extension;

use Bolt\Twig\Runtime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * HTML functionality Twig extension.
 */
class HtmlExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $safe = ['is_safe' => ['html']];
        $env = ['needs_environment' => true];

        return [
            // @codingStandardsIgnoreStart
            new TwigFunction('markdown', [Runtime\HtmlRuntime::class, 'dummy'], $safe),
            // @codingStandardsIgnoreEnd
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $safe = ['is_safe' => ['html']];
        $env = ['needs_environment' => true];

        return [
            // @codingStandardsIgnoreStart
            new TwigFilter('markdown', [Runtime\HtmlRuntime::class, 'dummy'], $safe),
            new TwigFilter('twig',     [Runtime\HtmlRuntime::class, 'dummy'], $env + $safe),
            // @codingStandardsIgnoreEnd
        ];
    }
}
