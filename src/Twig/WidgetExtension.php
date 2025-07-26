<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Widgets;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Widget functionality Twig extension.
 */
class WidgetExtension extends AbstractExtension
{
    public function __construct(
        private readonly Widgets $widgetRenderer
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];

        return [
            new TwigFunction('countwidgets', $this->countWidgets(...)),
            new TwigFunction('listwidgets', $this->listWidgets(...)),
            new TwigFunction('haswidgets', $this->hasWidgets(...)),
            new TwigFunction('widgets', $this->renderWidgetsForTarget(...), $safe),
            new TwigFunction('widget', $this->renderWidgetByName(...), $safe),
        ];
    }

    public function renderWidgetByName(string $name, array $params = []): string
    {
        return $this->widgetRenderer->renderWidgetByName($name, $params);
    }

    public function renderWidgetsForTarget(string $target, array $params = []): string
    {
        return $this->widgetRenderer->renderWidgetsForTarget($target, $params);
    }

    public function hasWidgets(string $target): bool
    {
        return count($this->listwidgets($target)) > 0;
    }

    public function listwidgets(string $target)
    {
        return $this->widgetRenderer->listWidgetsForTarget($target);
    }

    public function countwidgets(string $target): int
    {
        return count($this->listwidgets($target));
    }
}
