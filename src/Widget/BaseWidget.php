<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Extension\ExtensionInterface;
use Bolt\Widget\Exception\WidgetException;
use Cocur\Slugify\Slugify;
use Twig\Error\LoaderError;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

/**
 * BaseWidget can be used as easy starter pack or as a base for your own widgets.
 */
abstract class BaseWidget implements WidgetInterface
{
    use TwigTrait;
    use RequestTrait;
    use ResponseTrait;

    /** @var string */
    protected $name;

    /** @var string from Target enum */
    protected $target;

    /** @var string[] */
    protected $targets = [];

    /** @var string from RequestZone */
    protected $zone;

    /** @var ExtensionInterface */
    protected $extension;

    /** @var int */
    protected $priority = 0;

    /** @var string filename of Twig template */
    protected $template;

    /** @var string path to Twig templates folder */
    protected $templateFolder;

    /** @var ?string */
    protected $slug;

    /** @var int duration (in seconds) to cache output */
    protected $cacheDuration = 600;

    public function setName(string $name): WidgetInterface
    {
        $this->name = $name;
        $this->slug = null;

        return $this;
    }

    public function getName(): string
    {
        if ($this->name === null) {
            throw new WidgetException('Widget of class ' . self::class . ' does not have a name!');
        }

        return $this->name;
    }

    public function getTargets(): array
    {
        // backwards compatibility for $target
        if ($this->target && ! $this->hasTarget($this->target)) {
            $this->targets[] = $this->target;
        }

        if ($this->targets === null) {
            throw new WidgetException("Widget {$this->getName()} does not have Targets set");
        }

        return $this->targets;
    }

    /**
     * @param string[] $targets
     */
    public function setTargets(array $targets): WidgetInterface
    {
        $this->targets = $targets;

        return $this;
    }

    public function addTarget(string $target): WidgetInterface
    {
        if (! $this->hasTarget($target)) {
            $this->targets[] = $target;
        }

        return $this;
    }

    public function removeTarget(string $target): WidgetInterface
    {
        if ($this->hasTarget($target)) {
            unset($this->targets[$target]);
        }

        return $this;
    }

    public function hasTarget(string $target): bool
    {
        return is_array($this->targets) && in_array($target, $this->targets, true);
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

    public function injectExtension(ExtensionInterface $extension): void
    {
        $this->extension = $extension;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Method to 'invoke' the widget. Simple wrapper around the 'run' method,
     * which can be overridden in a custom Widget or trait
     */
    public function __invoke(array $params = []): ?string
    {
        return $this->run($params);
    }

    /**
     * Actual method that 'runs' the widget and returns the output. For reasons
     * of extensibility: Do not call directly, but call `$widget()` to invoke.
     */
    protected function run(array $params = []): ?string
    {
        if (array_key_exists('template', $params)) {
            $this->setTemplate($params['template']);
        }

        // Extension is set, and needs to be available in the template
        $params['extension'] = $this->extension;
        $params['widget'] = $this;

        if ($this instanceof TwigAwareInterface) {
            $this->addTwigLoader();

            try {
                $output = $this->getTwig()->render($this->getTemplate(), $params);
            } catch (LoaderError $e) {
                $output = "<div style='border: 1px solid #666; background-color: #FCF8E3; padding: 0.5rem;'><mark><strong>";
                $output .= sprintf("Could not render extension '%s'.</strong></mark><br>", $this->getName());
                $output .= sprintf('<code>%s</code><br>', $e->getMessage());
                $output .= sprintf('Did you mean to use <code>@%s/%s</code> instead?</mark></div>', $this->getSlug(), basename($this->getTemplate()));
            }
        } else {
            $output = $this->getTemplate();
        }

        return sprintf(
            '<div class="widget" id="widget-%s" name="%s">%s</div>',
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

    public function getTemplateFolder(): ?string
    {
        if ($this->templateFolder !== null) {
            return $this->templateFolder;
        }

        $reflection = new \ReflectionClass($this);

        $folder = dirname($reflection->getFilename()) . DIRECTORY_SEPARATOR . 'templates';
        if (realpath($folder)) {
            return realpath($folder);
        }

        $folder = dirname(dirname($reflection->getFilename())) . DIRECTORY_SEPARATOR . 'templates';
        if (realpath($folder)) {
            return realpath($folder);
        }

        $folder = dirname(dirname(dirname($reflection->getFilename()))) . DIRECTORY_SEPARATOR . 'templates';
        if (realpath($folder)) {
            return realpath($folder);
        }

        return null;
    }

    private function addTwigLoader(): void
    {
        /** @var FilesystemLoader|ChainLoader $twigLoaders */
        $twigLoaders = $this->getTwig()->getLoader();

        $twigLoaders = $twigLoaders instanceof ChainLoader ?
            $twigLoaders->getLoaders() :
            [$twigLoaders];

        foreach ($twigLoaders as $twigLoader) {
            if ($this->getTemplateFolder() && $twigLoader instanceof FilesystemLoader) {
                $twigLoader->addPath($this->getTemplateFolder(), $this->getSlug());
            }
        }
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

    public function getCacheDuration(): int
    {
        return $this->cacheDuration;
    }
}
