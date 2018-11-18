<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Helpers\Excerpt;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Content record functionality Twig extension.
 */
class RecordExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $safe = ['is_safe' => ['html']];
        $env = ['needs_environment' => true];

        return [
            new TwigFunction('excerpt', [$this, 'excerpt'], $safe),
            new TwigFunction('listtemplates', [$this, 'dummy']),
            new TwigFunction('pager', [$this, 'dummy_with_env'], $env + $safe),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $safe = ['is_safe' => ['html']];
        $deprecated = ['deprecated' => true];

        return [
            new TwigFilter('excerpt', [$this, 'excerpt'], $safe),
        ];
    }

    public function dummy($input = null)
    {
        return $input;
    }

    public function dummy_with_env(Environment $env, $input = null)
    {
        return $input;
    }

    /**
     * @param int $length
     */
    public function excerpt($text, $length = 100): string
    {
        $excerpter = new Excerpt($text);

        return $excerpter->getExcerpt((int) $length);
    }
}
