<?php

declare(strict_types=1);

namespace Bolt\Configuration;

class FileLocation
{
    private $key;
    private $name;
    private $basepath;
    private $showAll = false;

    public function __construct(string $key, string $name, string $basepath, bool $showAll)
    {
        $this->key = $key;
        $this->name = $name;
        $this->basepath = $basepath;
        $this->showAll = $showAll;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getName(): string
    {
        return $this->name;
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
