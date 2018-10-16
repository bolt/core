<?php declare(strict_types=1);

namespace Bolt\Twig\Extension;

use Bolt\Twig\Runtime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $safe = ['is_safe' => ['html']];

        return [
            // @codingStandardsIgnoreStart
            new TwigFunction('__',                 [Runtime\AdminRuntime::class, 'dummy'], $safe),
            // @codingStandardsIgnoreEnd
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $safe = ['is_safe' => ['html']];

        return [
            // @codingStandardsIgnoreStart
            new TwigFilter('__',       [Runtime\AdminRuntime::class, 'dummy']),
            new TwigFilter('ymllink',  [Runtime\AdminRuntime::class, 'ymllink'], $safe),
            // @codingStandardsIgnoreEnd
        ];
    }
}
