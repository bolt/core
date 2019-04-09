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
    public function getFunctions(): array
    {
        $safe = ['is_safe' => ['html']];
        $env = ['needs_environment' => true];

        return [
            new TwigFunction('snippet', [$this, 'getSnippet'], $env + $safe),
            new TwigFunction('snippetList', [$this, 'getSnippetList']),
        ];
    }

    public function getSnippet(Environment $twig, $name = null): string
    {
        return "-- Snippet ${name} --";
    }

    public function getSnippetList(): array
    {
        return [];
    }
}
