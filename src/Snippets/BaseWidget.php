<?php

declare(strict_types=1);

/**
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\Snippets;

use Twig\Environment;

class BaseWidget
{
    protected $name = 'Nameless widget';
    protected $type = 'widget';
    protected $target = Target::NOWHERE;
    protected $priority = 0;

    /** @var Environment */
    protected $twig;

    /** @var string */
    protected $template;

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setTarget(string $target): self
    {
        $this->target = $target;
        return $this;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }

    public function invoke(?string $template = null)
    {
        if ($template === null) {
            $template = $this->template;
        }

        return $this->twig->render($template);
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
}
