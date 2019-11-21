<?php

declare(strict_types=1);

namespace Bolt\Configuration;

class FileLocation
{
    private $key;
    private $name;
    private $basepath;
    private $showAll = false;
    private $icon;

    public function __construct(string $key, string $name, string $basepath, bool $showAll, string $icon)
    {
        $this->key = $key;
        $this->name = $name;
        $this->basepath = $basepath;
        $this->showAll = $showAll;
        $this->icon = $icon;
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
