<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Snippets\Manager;
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

    public function getWidget($env, $name)
    {
        return $this->snippetManager->getWidget($env, $name);
    }

    public function getWidgets($target)
    {
        return $this->snippetManager->getWidgets($target);
    }

    public function dummy(Environment $env, $input = null)
    {
        // @todo See Github issue https://github.com/bolt/four/issues/135
        return '<!-- Widget "' . $input . '" -->';
    }
}
