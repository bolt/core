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
    /** @var Widgets */
    private $widgetRenderer;

    public function __construct(Widgets $widgets)
    {
        $this->widgetRenderer = $widgets;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFunction('countwidgets', [$this, 'dummy']),
            new TwigFunction('listwidgets', [$this, 'dummy']),
            new TwigFunction('haswidgets', [$this, 'dummy']),
            new TwigFunction('widgets', [$this, 'renderWidgetsForTarget'], $safe),
            new TwigFunction('widget', [$this, 'renderWidgetByName'], $safe),
        ];
    }

    public function renderWidgetByName(string $name): string
    {
        return $this->widgetRenderer->renderWidgetByName($name);
    }

    public function renderWidgetsForTarget(string $target): string
    {
        return $this->widgetRenderer->renderWidgetsForTarget($target);
    }

    public function dummy($input = null): string
    {
        // @todo See Github issue https://github.com/bolt/four/issues/135
        return '<!-- Widget "' . $input . '" -->';
    }
}
