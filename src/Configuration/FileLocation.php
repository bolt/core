<?php

declare(strict_types=1);

namespace Bolt\Configuration;

class FileLocation
{
    /** @var string */
    private $key;

    /** @var string */
    private $name;

    /** @var string */
    private $basepath;

    /** @var bool */
    private $showAll = false;

    /** @var string */
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
