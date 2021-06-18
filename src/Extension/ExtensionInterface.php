<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Composer\Package\CompletePackageInterface;

interface ExtensionInterface
{
    public const CONTAINER_TAG = 'bolt.extension';

    public function getName(): string;

    public function getClass(): string;

    public function injectObjects(array $objects): void;

    public function initialize(): void;

    public function initializeCli(): void;

    public function install(): void;

    public function getComposerPackage(): ?CompletePackageInterface;
}
