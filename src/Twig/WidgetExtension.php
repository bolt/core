<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Snippets;
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

        return [
            new TwigFunction('countwidgets', [$this, 'dummy'], $safe),
            new TwigFunction('listwidgets', [$this, 'dummy'], $safe),
            new TwigFunction('haswidgets', [$this, 'dummy'], $safe),
            new TwigFunction('widgets', [$this, 'getWidgets'], $safe),
            new TwigFunction('widget', [$this, 'getWidget'], $safe),
        ];
    }

    public function getWidget(string $name): string
    {
        return $this->snippets->getWidget($name);
    }

    public function getWidgets(string $target): string
    {
        return $this->snippets->getWidgets($target);
    }

    public function dummy($input = null): string
    {
        // @todo See Github issue https://github.com/bolt/four/issues/135
        return '<!-- Widget "' . $input . '" -->';
    }
}
