<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Snippets;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Widget functionality Twig extension.
 */
class WidgetExtension extends AbstractExtension
{
    /** @var Snippets */
    private $snippets;

    public function __construct(Snippets $snippets)
    {
        $this->snippets = $snippets;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = ['is_safe' => ['html']];
        $env = ['needs_environment' => true];

        return [
            new TwigFunction('countwidgets', [$this, 'dummy'], $safe + $env),
            new TwigFunction('listwidgets', [$this, 'dummy'], $safe),
            new TwigFunction('haswidgets', [$this, 'dummy'], $safe + $env),
            new TwigFunction('widgets', [$this, 'getWidgets'], $safe + $env),
            new TwigFunction('widget', [$this, 'getWidget'], $safe + $env),
        ];
    }

    public function getWidget(Environment $twig, string $name): string
    {
        return $this->snippets->getWidget($name, $twig);
    }

    public function getWidgets(Environment $twig, string $target): string
    {
        return $this->snippets->getWidgets($target, $twig);
    }

    public function dummy(Environment $twig, $input = null): string
    {
        // @todo See Github issue https://github.com/bolt/four/issues/135
        return '<!-- Widget "' . $input . '" -->';
    }
}
