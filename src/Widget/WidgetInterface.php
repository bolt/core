<?php

declare(strict_types=1);

namespace Bolt\Widget;

interface WidgetInterface
{
    public function getName(): string;

    public function getType(): string;

    /**
     * @return string from Bolt\Snippet\Target constants enum
     */
    public function getTarget(): string;

    public function getPriority(): int;

    public function getTemplate(): string;

    /**
     * @return string from Bolt\Snippet\RequestZone constants enum
     */
    public function getZone(): string;

    public function getSlug(): string;

    public function __invoke(?string $template = null, array $params = []): string;
}
