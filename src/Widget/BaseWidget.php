<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Snippet\RequestZone;
use Bolt\Snippet\Target;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class BaseWidget implements WidgetInterface
{
    protected $name = 'Nameless widget';
    protected $type = 'widget';
    protected $target = Target::NOWHERE;
    protected $zone = RequestZone::EVERYWHERE;
    protected $priority = 0;

    /** @var string */
    protected $template = '';

    /** @var Response */
    protected $response;

    /** @var ?string */
    protected $slug;

    /** @var Request */
    private $request;

    /** @var Environment */
    private $twig;

    public function setName(string $name): WidgetInterface
    {
        $this->name = $name;
        $this->slug = null;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setType(string $type): WidgetInterface
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setTarget(string $target): WidgetInterface
    {
        $this->target = $target;

        return $this;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function setPriority(int $priority): WidgetInterface
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
            $output = $this->twig->render($this->getTemplate(), $params);
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

    public function setTemplate(string $template): WidgetInterface
    {
        $this->template = $template;

        return $this;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTwig(Environment $twig): WidgetInterface
    {
        $this->twig = $twig;

        return $this;
    }

    public function getTwig(): Environment
    {
        return $this->twig;
    }

    public function setRequest(Request $request): WidgetInterface
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setResponse(?Response $response = null): WidgetInterface
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

    public function setZone(string $zone): WidgetInterface
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
