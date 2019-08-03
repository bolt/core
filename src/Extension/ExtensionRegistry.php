<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Widgets;

class ExtensionRegistry
{
    /** @var ExtensionInterface[] * */
    protected $extensions = [];

    /** @var array */
    protected $extensionClasses = [];

    public function add(string $extensionClass): void
    {
        $this->extensionClasses[] = $extensionClass;
    }

    private function getExtensionClasses(): array
    {
        return $this->extensionClasses;
    }

    /** @return ExtensionInterface[] */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function initializeAll(Widgets $widgets): void
    {
        foreach ($this->getExtensionClasses() as $extensionClass) {
            $extension = new $extensionClass();
            $extension->injectObjects($widgets);
            $extension->initialize();

            $this->extensions[$extensionClass] = $extension;
        }
    }
}
