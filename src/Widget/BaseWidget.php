<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Snippet\Target;
use Bolt\Snippet\Zone;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class BaseWidget
{
    protected $name = 'Nameless widget';
    protected $type = 'widget';
    protected $target = Target::NOWHERE;
    protected $zone = Zone::EVERYWHERE;
    protected $priority = 0;
    protected $context = [];

    /** @var Environment */
    protected $twig;

    /** @var string */
    protected $template;

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

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

    public function setTwig(Environment $twig): self
    {
        $this->twig = $twig;

        return $this;
    }

    public function __invoke(?string $template = null): string
    {
        if ($template === null) {
            $template = $this->template;
        }

        if ($this instanceof TwigAware) {
            $output = $this->twig->render($template, $this->context);
        } else {
            $output = $template;
        }

        return sprintf(
            '<div id="widget-%s" name="%s">%s</div>',
            $this->getSlug(),
            $this->getName(),
            $output
        );
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setResponse(?Response $response = null): self
    {
        if ($response !== null) {
            $this->response = $response;
        }

        return $this;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setZone(string $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getZone(): string
    {
        return $this->zone;
    }

    public function getSlug(): string
    {
        $slugify = Slugify::create();

        return $slugify->slugify($this->name);
    }
}
