<?php

namespace Bolt\Storage\Strategy;

use Bolt\Storage\Builder\ContentBuilder;
use Bolt\Storage\Strategy\Traits\MultipleKeyValueParserTrait;

class MultipleValueSelectorArgumentStrategy implements ContentArgumentStrategyInterface
{
    use MultipleKeyValueParserTrait;

    public function extendsByArguments(ContentBuilder $content, string $field, string $value): void
    {
    }

    public function shouldBeCalled(): bool
    {
        $arguments = func_get_args();
        if (count($arguments) === 2) {
            [$key, $value] = $arguments;
            return preg_match('/\|{3}|\&{3}/', $key) && preg_match('/\|{3}|\&{3}/', $value);
        }

        return false;
    }
}
