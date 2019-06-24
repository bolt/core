<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Exception\WidgetException;
use Twig\Environment;

trait TwigTrait
{
    /** @var Environment */
    private $twig;

    public function setTwig(Environment $twig): self
    {
        $this->twig = $twig;

        return $this;
    }

    public function getTwig(): Environment
    {
        if ($this->twig === null) {
            throw new WidgetException("Widget {$this->getName()} does not have Twig set");
        }
        return $this->twig;
    }
}
