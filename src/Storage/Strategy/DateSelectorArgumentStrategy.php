<?php

namespace Bolt\Storage\Strategy;

use Bolt\Storage\Builder\ContentBuilder;
use Bolt\Storage\Builder\Filter\GraphFilter;
use Bolt\Storage\Strategy\Traits\SingleValueParserTrait;
use DateTime;

class DateSelectorArgumentStrategy implements ContentArgumentStrategyInterface
{
    use SingleValueParserTrait;

    public function extendsByArguments(ContentBuilder $content, string $field, string $value): void
    {
        [$operator, $value] = $this->parseValue($value);

        $date = new DateTime($value);

        $content->addFilter(
            GraphFilter::createSimpleFilter($this->getFieldForFilter($operator, $value), $date->format('Y-m-d H:i:s'))
        );
    }

    public function shouldBeCalled(): bool
    {
        $arguments = func_get_args();
        if (count($arguments) === 1) {
            return strtotime($arguments[0]) !== false;
        }

        return false;
    }
}
