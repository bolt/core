<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Extension\ExtensionRegistry;
use Tightenco\Collect\Support\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Widget functionality Twig extension.
 */
class ExtensionExtension extends AbstractExtension
{
    /** @var ExtensionRegistry */
    private $registry;

    public function __construct(ExtensionRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getTests()
    {
        return [
            new TwigTest('extension', [$this, 'extensionExists']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('extension_exists', [$this, 'extensionExists']),
            new TwigFunction('extensions', [$this, 'getExtensions']),
        ];
    }

    public function getExtensions(): Collection
    {
        $extensions = $this->registry->getExtensions();

        $rows = [];

        foreach ($extensions as $extension) {
            $rows[] = [
                'package' => $extension->getComposerPackage()->getName(),
                'class' => $extension->getClass(),
                'name' => $extension->getName(),
            ];
        }

        return new Collection($rows);
    }

    public function extensionExists(string $name): bool
    {
        $extensions = $this->getExtensions();

        return $extensions->where('package', $name)->isNotEmpty() ||
            $extensions->where('class', $name)->isNotEmpty() ||
            $extensions->where('name', $name)->isNotEmpty();
    }
}
