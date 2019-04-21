<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * BaseWidget can be used as easy starter pack or as a base for your own widgets.
 */
abstract class BaseWidget implements WidgetInterface
{
    /** @var string */
    protected $name;

    /** @var string from Target enum */
    protected $target;

    /** @var string from RequestZone */
    protected $zone;

    /** @var int */
    protected $priority = 0;

    /** @var string path to Twig template */
    protected $template;

    /** @var Response */
    protected $response;

    /** @var ?string */
    protected $slug;

    /** @var Request */
    private $request;

    /** @var Environment */
    private $twig;

    public function setName(string $name): self
    {
        $this->name = $name;
        $this->slug = null;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function __invoke(array $params = []): string
    {
        if (array_key_exists('template', $params)) {
            $this->setTemplate($params['template']);
        }

        if ($this instanceof TwigAware) {
            $output = $this->getTWig()->render($this->getTemplate(), $params);
        } else {
            $output = $this->getTemplate();
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

    public function setTwig(Environment $twig): self
    {
        $this->twig = $twig;

        return $this;
    }

    public function getTwig(): Environment
    {
        return $this->twig;
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

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getResponse(): Response
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
        if ($this->slug === null) {
            $slugify = Slugify::create();
            $this->slug = $slugify->slugify($this->name);
        }

        return $this->slug;
    }
}
