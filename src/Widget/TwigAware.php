<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Twig\Environment;

/**
 * Interface TwigAware - Widgets that make use of a template to render their
 * contents need to implement this interface, in order to access the current
 * Twig\Environment and to render a Twig template.
 */
interface TwigAware
{
    public function setTwig(Environment $twig): WidgetInterface;

    public function getTwig(): Environment;
}
