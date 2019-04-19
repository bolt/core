<?php

declare(strict_types=1);

namespace Bolt\Widget;

interface WidgetInterface
{
    public function getName(): string;

    public function getType(): string;

    public function getTarget(): string;

    public function getPriority(): int;

    public function getTemplate(): string;

    public function getZone(): string;

    public function getSlug(): string;

    public function __invoke(?string $template = null): string;
}
