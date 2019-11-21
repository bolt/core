<?php

declare(strict_types=1);

namespace Bolt\Extension;

interface ExtensionInterface
{
    public const CONTAINER_TAG = 'bolt.extension';

    public function getName(): string;

    public function getClass(): string;

    public function injectObjects(array $objects): void;

    public function initialize(): void;
}
