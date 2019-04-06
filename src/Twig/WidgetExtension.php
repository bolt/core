<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Snippet\Manager;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Widget functionality Twig extension.
 */
class WidgetExtension extends AbstractExtension
{
    /** @var Manager */
    private $snippetManager;

    public function __construct(Manager $snippetManager)
    {
        $this->snippetManager = $snippetManager;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
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

    public function getWidget(Environment $twig, $name)
    {
        return $this->snippetManager->getWidget($twig, $name);
    }

    public function getWidgets(Environment $twig, $target)
    {
        return $this->snippetManager->getWidgets($twig, $target);
    }

    public function dummy(Environment $twig, $input = null)
    {
        // @todo See Github issue https://github.com/bolt/four/issues/135
        return '<!-- Widget "' . $input . '" -->';
    }
}
