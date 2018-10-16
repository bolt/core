<?php declare(strict_types=1);

namespace Bolt\Twig\Runtime;

use Twig\Environment;

/**
 * Bolt specific Twig functions and filters for HTML.
 */
class HtmlRuntime
{
    public function dummy($input = null)
    {
        return $input;
    }

    /**
     * Formats the given string as Twig in HTML.
     *
     * @param Environment $env
     * @param string      $snippet
     * @param array       $context
     *
     * @return string Twig output
     */
    public function twig(Environment $env, $snippet, $context = [])
    {
        $template = $env->createTemplate((string) $snippet);

        return twig_include($env, $context, $template, [], true, false, true);
    }
}
