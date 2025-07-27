<?php

declare(strict_types=1);

namespace Bolt\Storage;

use Doctrine\ORM\Query\Expr;
use Exception;

/**
 *  Handler class to convert the DSL for content query parameters
 *  into equivalent ORM expressions.
 *
 * @author Ross Riley <riley.ross@gmail.com>
 */
class QueryParameterParser
{
    /** @var string */
    public $alias;

    /** @var string */
    protected $key;

    /** @var array */
    protected $valueMatchers = [];

    /** @var Filter[] */
    protected $filterHandlers = [];

    public function __construct(
        protected Expr $expr
    ) {
        $this->setupDefaults();
    }

    public function setupDefaults(): void
    {
        $word = "[\p{L}\p{N}_\/]+";

        // @codingStandardsIgnoreStart
        $this->addValueMatcher("<\s?({$word})", [
            'value' => '$1',
            'operator' => 'lt',
        ]);
        $this->addValueMatcher("<=\s?({$word})", [
            'value' => '$1',
            'operator' => 'lte',
        ]);
        $this->addValueMatcher(">=\s?({$word})", [
            'value' => '$1',
            'operator' => 'gte',
        ]);
        $this->addValueMatcher(">\s?({$word})", [
            'value' => '$1',
            'operator' => 'gt',
        ]);
        $this->addValueMatcher('!$', [
            'value' => '',
            'operator' => 'isNotNull',
        ]);
        $this->addValueMatcher("!\s?({$word})", [
            'value' => '$1',
            'operator' => 'neq',
        ]);
        $this->addValueMatcher('!\s?\[([\p{L}\p{N} ,]+)\]', [
            'value' => fn ($val) => explode(',', (string) $val),
            'operator' => 'notIn',
        ]);
        $this->addValueMatcher('\[([\p{L}\p{N} ,]+)\]', [
            'value' => fn ($val) => explode(',', (string) $val),
            'operator' => 'in',
        ]);
        $this->addValueMatcher("(%{$word}|{$word}%|%{$word}%)", [
            'value' => '$1',
            'operator' => 'like',
        ]);
        $this->addValueMatcher("({$word})", [
            'value' => '$1',
            'operator' => 'eq',
        ]);
        $this->addValueMatcher('()', [
            'value' => '$1',
            'operator' => 'eq',
        ]);
        // @codingStandardsIgnoreEnd

        $this->addFilterHandler($this->defaultFilterHandler(...));
        $this->addFilterHandler($this->booleanValueHandler(...));
        $this->addFilterHandler($this->numericValueHandler(...));
        $this->addFilterHandler($this->multipleValueHandler(...));
        $this->addFilterHandler($this->multipleKeyAndValueHandler(...));
        $this->addFilterHandler($this->incorrectQueryHandler(...));
    }

    /**
     * Sets the select alias to be used in sql queries.
     */
    public function setAlias(string $alias): void
    {
        $this->alias = $alias . '.';
    }

    /**
     * Runs the keys/values through the relevant parsers.
     */
    public function getFilter(string $key, $value = null): ?Filter
    {
        /** @var callable $callback */
        foreach ($this->filterHandlers as $callback) {
            $result = $callback($key, $value, $this->expr);
            if ($result instanceof Filter) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Handles some errors in key/value string formatting.
     */
    public function incorrectQueryHandler(string $key, $value, Expr $expr)
    {
        if (is_string($value) === false) {
            return null;
        }

        if (mb_strpos($value, '&&') && mb_strpos($value, '||')) {
            throw new Exception('Mixed && and || operators are not supported', 1);
        }
    }

    /**
     * This handler processes 'triple pipe' queries as implemented in Bolt
     * It looks for three pipes in the key and value and creates an OR composite
     * expression for example: 'username|||email':'fred|||pete'.
     */
    public function multipleKeyAndValueHandler(string $key, $value, Expr $expr): ?Filter
    {
        if (is_string($value) === false) {
            return null;
        }

        if (! mb_strpos($key, '|||')) {
            return null;
        }

        $keys = preg_split('/ *(\|\|\|) */', $key);
        $inputKeys = $keys;
        $values = preg_split('/ *(\|\|\|) */', $value);
        $values = array_pad($values, count($keys), end($values));

        $filterParams = [];
        $parts = [];
        $count = 1;

        foreach (array_combine($keys, $values) as $key => $val) {
            $multipleValue = $this->multipleValueHandler($key, $val, $this->expr);
            if ($multipleValue) {
                $filter = $multipleValue->getExpression();
                $filterParams += $multipleValue->getParameters();
            } else {
                $val = $this->parseValue($val);
                // @todo check what type of field $key is
                $placeholder = $key . '_' . $count;
                $filterParams[$placeholder] = $val['value'];
                $exprMethod = $val['operator'];
                $filter = $this->expr->{$exprMethod}($this->alias . $key, ':' . $placeholder);
            }

            $parts[] = $filter;
            ++$count;
        }

        $filter = new Filter();
        $filter->setKey($inputKeys);
        $filter->setExpression($expr->orX(...$parts));
        $filter->setParameters($filterParams);

        return $filter;
    }

    /**
     * This handler processes multiple value queries as defined in the Bolt 'Fetching Content'
     * documentation. It allows a value to be parsed to and AND/OR expression.
     *
     * For example, this handler will correctly parse values like:
     *     'username': 'fred||bob'
     *     'id': '<5 && !1'
     */
    public function multipleValueHandler(string $key, $value, Expr $expr): ?Filter
    {
        if (is_string($value) === false) {
            return null;
        }

        if (mb_strpos($value, '&&') === false && mb_strpos($value, '||') === false) {
            return null;
        }

        $values = preg_split('/ *(&&|\|\|) */', $value, -1, PREG_SPLIT_DELIM_CAPTURE);
        $op = $values[1];

        $comparison = 'andX';

        if ($op === '&&') {
            $comparison = 'andX';
        } elseif ($op === '||') {
            $comparison = 'orX';
        }

        $values = array_diff($values, ['&&', '||']);

        $filterParams = [];
        $parts = [];
        $count = 1;

        foreach ($values as $val) {
            $val = $this->parseValue($val);
            $placeholder = $key . '_' . $count;
            $filterParams[$placeholder] = $val['value'];
            $exprMethod = $val['operator'];
            $parts[] = $this->expr->{$exprMethod}($this->alias . $key, ':' . $placeholder);
            ++$count;
        }

        $filter = new Filter();
        $filter->setKey($key);
        $filter->setExpression($expr->{$comparison}(...$parts));
        $filter->setParameters($filterParams);

        return $filter;
    }

    /**
     * The boolean handler handles single boolean values.
     * For example, checkbox field values.
     */
    public function booleanValueHandler(string $key, $value, Expr $expr): ?Filter
    {
        if (! is_bool($value)) {
            return null;
        }

        $filter = $this->defaultFilterHandler($key, $value, $expr);

        // Ineffective way to set the value, if it is a string.
        foreach ($filter->getParameters() as $key => $val) {
            if ($val === (string) $value) {
                // Put it back as a boolean.
                $filter->setParameter($key, $value);
            }
        }

        return $filter;
    }

    /**
     * The numeric handler handles single numeric values.
     * For example, content select field values.
     */
    public function numericValueHandler(string $key, $value, Expr $expr): ?Filter
    {
        if (! is_numeric($value)) {
            return null;
        }

        $filter = $this->defaultFilterHandler($key, $value, $expr);

        // Ineffective way to set the value, if it is a string.
        foreach ($filter->getParameters() as $key => $val) {
            if ($val === (string) $value) {
                // Put it back as a boolean.
                $filter->setParameter($key, $value);
            }
        }

        return $filter;
    }

    /**
     * The default handler is the last to be run and handles simple value parsing.
     *
     * @param string|array|bool $value
     */
    public function defaultFilterHandler(string $key, $value, Expr $expr): Filter
    {
        $filter = new Filter();
        $filter->setKey($key);

        if (is_array($value)) {
            $count = 1;

            $composite = $expr->andX();

            foreach ($value as $paramName => $valueItem) {
                $val = $this->parseValue((string) $valueItem);
                $placeholder = sprintf('%s_%s_%s', $key, $paramName, $count);
                $exprMethod = $val['operator'];
                $composite->add($expr->{$exprMethod}($this->alias . $key, ':' . $placeholder));
                $filter->setParameter($placeholder, $val['value']);

                ++$count;
            }
            $filter->setExpression($composite);

            return $filter;
        }

        $val = $this->parseValue((string) $value);

        $placeholder = $key . '_1';
        $exprMethod = $val['operator'];

        $filter->setExpression($expr->andX($expr->{$exprMethod}($this->alias . $key, ':' . $placeholder)));
        $filter->setParameters([$placeholder => $val['value']]);

        return $filter;
    }

    /**
     * This method uses the defined value matchers to parse a passed in value.
     *
     * The following component parts will be returned in the array:
     * [
     *     'value' => <the value remaining after the parse>
     *     'operator' => <the operator that should be used>
     *     'matched' => <the pattern that the value matched>
     * ]
     */
    public function parseValue(string $value): array
    {
        foreach ($this->valueMatchers as $matcher) {
            $regex = sprintf('/%s/u', $matcher['token']);
            $values = $matcher['params'];
            if (preg_match($regex, $value)) {
                if (is_callable($values['value'])) {
                    preg_match($regex, $value, $output);
                    $values['value'] = $values['value']($output[1]);
                } else {
                    $values['value'] = preg_replace($regex, (string) $values['value'], $value);
                }
                $values['matched'] = $matcher['token'];

                return $values;
            }
        }

        throw new Exception(sprintf('No matching value found for "%s"', $value));
    }

    /**
     * The goal of this class is to turn any key:value into a Filter class.
     * Adding a handler here will push the new filter callback onto the top
     * of the Queue along with the built in defaults.
     *
     * Note: the callback should either return nothing or an instance of
     * \Bolt\Storage\Filter
     */
    public function addFilterHandler(callable $handler): void
    {
        array_unshift($this->filterHandlers, $handler);
    }

    /**
     * Adds an additional token to parse for value parameters.
     *
     * This gives the ability to define additional value -> operator matches
     *
     * @param string $token Regex pattern to match against
     * @param array $params Options to provide to the matched param
     * @param bool $priority If set item will be prepended to start of list
     */
    public function addValueMatcher(string $token, array $params = [], $priority = null): void
    {
        if ($priority) {
            array_unshift($this->valueMatchers, [
                'token' => $token,
                'params' => $params,
            ]);
        } else {
            $this->valueMatchers[] = [
                'token' => $token,
                'params' => $params,
            ];
        }
    }
}
