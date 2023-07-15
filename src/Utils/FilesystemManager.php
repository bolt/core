<?php

namespace Bolt\Utils;

use League\Flysystem\FilesystemOperator;

class FilesystemManager
{
    /** @var array<FilesystemOperator> */
    private $filesystems;

    public function __construct(array $filesystems)
    {
        $this->filesystems = $filesystems;
    }

    public function get(string $name): FilesystemOperator
    {
        return $this->filesystems[$name];
    }
}
