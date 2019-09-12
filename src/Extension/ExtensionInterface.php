<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Bolt\Configuration\Config;
use Bolt\Widgets;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

interface ExtensionInterface
{
    public const CONTAINER_TAG = 'bolt.extension';

    public function getName(): string;

    public function getClass(): string;

    public function injectObjects(Widgets $widgets, Config $config, Environment $twig, EventDispatcherInterface $dispatcher): void;

    public function initialize(): void;
}
