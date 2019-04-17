<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Twig\Environment;

/**
 * Trait TwigAware - Widgets that make use of a template to render their
 * contents need to use this Trait, in order to access the current
 * Twig\Environment and to render a Twig template.
 */
trait TwigAware
{
    /** @var Environment */
    protected $twig;

    public function setTwig(Environment $twig): WidgetInterface
    {
        $this->twig = $twig;

        return $this;
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }
}
