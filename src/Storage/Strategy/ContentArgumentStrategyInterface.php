<?php

namespace Bolt\Storage\Strategy;

use Bolt\Storage\Builder\ContentBuilder;

interface ContentArgumentStrategyInterface
{
    public function extendsByArguments(ContentBuilder $content, string $field, string $value): void;

    public function shouldBeCalled(): bool;
}
