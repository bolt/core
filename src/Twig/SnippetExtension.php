<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SnippetExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $safe = ['is_safe' => ['html']];
        $env = ['needs_environment' => true];

        return [
            new TwigFunction('snippet', [$this, 'getSnippet'], $env + $safe),
            new TwigFunction('snippetList', [$this, 'getSnippetList']),
        ];
    }

    public function getSnippet(Environment $twig, ?string $name = null)
    {
        return "-- Snippet ${name} --";
    }

    public function getSnippetList()
    {
        return [];
    }
}
