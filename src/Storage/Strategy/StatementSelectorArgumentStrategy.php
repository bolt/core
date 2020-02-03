<?php

namespace Bolt\Storage\Strategy;

use Bolt\Storage\Builder\ContentBuilder;
use Bolt\Storage\Exception\WrongSelectorArgumentException;

class StatementSelectorArgumentStrategy implements ContentArgumentStrategyInterface
{
    public const SELECTORS = ['limit', 'order'];

    public function extendsByArguments(ContentBuilder $content, string $field, string $value): void
    {
        if (in_array($field, self::SELECTORS) === false) {
            throw new WrongSelectorArgumentException($field);
        }

        if ($field === 'limit') {
            $content->setLimit((int) $value);
            return;
        }

        if ($field === 'order' && mb_strpos($value, ',') !== false) {
            $orders = array_map(function($element) {
                return trim($element);
            }, explode(',', $value));
            foreach ($orders as $val) {
                $direction = 'ASC';
                if ($val[0] === '-') {
                    $direction = 'DESC';
                    $val = substr($val, 1);
                }
                $content->addOrder($val, $direction);
            }

            return;
        }

        $direction = 'ASC';
        if ($value[0] === '-') {
            $direction = 'DESC';
            $value = substr($value, 1);
        }
        $content->setOrder($value, $direction);
    }

    public function shouldBeCalled(): bool
    {
        $arguments = func_get_args();
        if (count($arguments) === 1) {
            return in_array($arguments[0], self::SELECTORS);
        }

        return false;
    }
}
