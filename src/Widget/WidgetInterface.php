<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Symfony\Component\HttpFoundation\Response;

interface WidgetInterface
{
    public function setName(string $name): self;

    public function getName(): string;

    public function setType(string $type): self;

    public function getType(): string;

    public function setTarget(string $target): self;

    public function getTarget(): string;

    public function setPriority(int $priority): self;

    public function getPriority(): int;

    public function setTemplate(string $template): self;

    public function getTemplate(): string;

    public function setResponse(?Response $response = null): self;

    public function getResponse(): ?Response;

    public function setZone(string $zone): self;

    public function getZone(): string;

    public function getSlug(): string;
}
