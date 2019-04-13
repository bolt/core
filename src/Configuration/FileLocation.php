<?php

declare(strict_types=1);

namespace Bolt\Configuration;

class FileLocation
{
    private $key;
    private $name;
    private $basepath;
    private $show_all = false;

    public function __construct(string $key, string $name, string $basepath, bool $show_all)
    {
        $this->key = $key;
        $this->name = $name;
        $this->basepath = $basepath;
        $this->show_all = $show_all;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getBasepath(): string
    {
        return $this->basepath;
    }

    public function setBasepath(string $basepath): self
    {
        $this->basepath = $basepath;
        return $this;
    }

    public function isShowAll(): bool
    {
        return $this->show_all;
    }

    public function setShowAll(bool $show_all): self
    {
        $this->show_all = $show_all;
        return $this;
    }
}
