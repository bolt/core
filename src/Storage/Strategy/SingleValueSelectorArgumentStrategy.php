<?php

namespace Bolt\Storage\Strategy;

use Bolt\Storage\Builder\ContentBuilder;
use Bolt\Storage\Builder\Filter\GraphFilter;
use Bolt\Storage\Strategy\Traits\SingleValueParserTrait;

class SingleValueSelectorArgumentStrategy implements ContentArgumentStrategyInterface
{
    use SingleValueParserTrait;

    public function extendsByArguments(ContentBuilder $content, string $field, string $value): void
    {
        [$operator, $value] = $this->parseValue($value);

        $content->addFilter(GraphFilter::createSimpleFilter($this->getFieldForFilter($operator, $value), $value));
    }

    public function shouldBeCalled(): bool
    {
        $arguments = func_get_args();
        if (count($arguments) === 1) {
            return preg_match('/\|{2}|\&{2}/', $arguments[0]) === 0;
        }

        return false;
    }
}
