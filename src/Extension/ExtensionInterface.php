<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Widgets;

interface ExtensionInterface
{
    public const CONTAINER_TAG = 'bolt.extension';

    public function getName(): string;

    public function getClass(): string;

    public function injectObjects(Widgets $widgets): void;

    public function initialize(): void;
}
