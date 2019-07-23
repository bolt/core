<?php

declare(strict_types=1);

namespace Bolt\Extension;

class ExtensionRegistry
{
    /** @var ExtensionInterface[] **/
    protected $extensions = [];

    public function add(ExtensionInterface $extension): void
    {
        $this->extensions[\get_class($extension)] = $extension;
    }

    /** @return ExtensionInterface[] */
    public function getExtensions(): array
    {
        return $this->extensions;
    }
}