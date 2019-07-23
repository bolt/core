<?php

declare(strict_types=1);

namespace Bolt\Extension;

interface ExtensionInterface
{
    public const CONTAINER_TAG = 'bolt.extension';
    public function getName(): string;
}