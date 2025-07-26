<?php

declare(strict_types=1);

namespace Bolt\Configuration;

class FileLocation
{
    public function __construct(
        private readonly string $key,
        private readonly string $name,
        private readonly string $basepath,
        private readonly bool $showAll,
        private readonly string $icon
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getBasepath(): string
    {
        return $this->basepath;
    }

    public function isShowAll(): bool
    {
        return $this->showAll;
    }
}
