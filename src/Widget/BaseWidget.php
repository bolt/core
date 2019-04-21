<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Exception\WidgetException;
use Cocur\Slugify\Slugify;

/**
 * BaseWidget can be used as easy starter pack or as a base for your own widgets.
 */
abstract class BaseWidget implements WidgetInterface
{
    use TwigTrait, RequestTrait, ResponseTrait;

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

    /** @var ?string */
    protected $slug;

    public function setName(string $name): self
    {
        $this->name = $name;
        $this->slug = null;

        return $this;
    }

    public function getName(): string
    {
        if ($this->name === null) {
            throw new WidgetException('Widget of class '.self::class.' does not have a name!');
        }
        return $this->name;
    }

    public function setTarget(string $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getTarget(): string
    {
        if ($this->target === null) {
            throw new WidgetException("Widget {$this->getName()} does not have Target set");
        }
        return $this->target;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPriority(): int
    {
        if ($this->priority === null) {
            throw new WidgetException("Widget {$this->getName()} does not have priority set");
        }
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
        if ($this->template === null) {
            throw new WidgetException("Widget {$this->getName()} does not have template set");
        }
        return $this->template;
    }

    public function setZone(string $zone): self
    {
        $this->zone = $zone;

        return $this;
    }

    public function getZone(): string
    {
        if ($this->zone === null) {
            throw new WidgetException("Widget {$this->getName()} does not have Zone set");
        }
        return $this->zone;
    }

    public function getSlug(): string
    {
        if ($this->slug === null) {
            $this->slug = Slugify::create()->slugify($this->getName());
        }

        return $this->slug;
    }
}
