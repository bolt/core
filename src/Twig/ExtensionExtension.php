<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Extension\ExtensionRegistry;
use Illuminate\Support\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Widget functionality Twig extension.
 */
class ExtensionExtension extends AbstractExtension
{
    public function __construct(
        private readonly ExtensionRegistry $registry
    ) {
    }

    public function getTests(): array
    {
        return [
            new TwigTest('extension', $this->extensionExists(...)),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('extension_exists', $this->extensionExists(...)),
            new TwigFunction('extensions', $this->getExtensions(...)),
        ];
    }

    public function getExtensions(): Collection
    {
        $extensions = $this->registry->getExtensions();

        $rows = [];

        foreach ($extensions as $extension) {
            $packageName = $extension->getComposerPackage() ? $extension->getComposerPackage()->getName() : 'No Package';
            $rows[] = [
                'package' => $packageName,
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
