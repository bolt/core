<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Helpers\Excerpt;
use Twig\Environment;
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
            new TwigFunction('pager', [$this, 'pager'], $env + $safe),
            new TwigFunction('menu', [$this, 'pager'], $env + $safe),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $safe = ['is_safe' => ['html']];

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

    public function pager(Environment $env, string $template = '')
    {
        // @todo See Github issue https://github.com/bolt/four/issues/254
        return '[pager placeholder]';
    }

    public function menu(Environment $env, string $template = '')
    {
        // @todo See Github issue https://github.com/bolt/four/issues/253
        return '[menu placeholder]';
    }

    public static function excerpt(string $text, int $length = 100): string
    {
        $excerpter = new Excerpt($text);

        return $excerpter->getExcerpt($length);
    }
}
