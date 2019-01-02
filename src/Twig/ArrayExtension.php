<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Bolt specific Twig functions and filters that provide array manipulation.
 *
 * @internal
 */
final class ArrayExtension extends AbstractExtension
{
    /** @var string */
    private $orderOn = '';

    /** @var int */
    private $orderAscending = 1;

    /** @var ?string */
    private $orderOnSecondary = null;

    /** @var ?boolean */
    private $orderAscendingSecondary = null;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFunction('unique', [$this, 'unique'], $safe),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('order', [$this, 'order']),
            new TwigFilter('shuffle', [$this, 'shuffle']),
        ];
    }

    public function dummy($input = null)
    {
        return $input;
    }

    /**
     * Takes two arrays and returns a compiled array of unique, sorted values.
     */
    public function unique(array $arr1, array $arr2): array
    {
        $merged = array_unique(array_merge($arr1, $arr2), SORT_REGULAR);
        $compiled = [];

        foreach ($merged as $key => $val) {
            if (is_array($val) && array_values($val) === $val) {
                $compiled[$key] = $val;
            } else {
                $compiled[$val] = $val;
            }
        }

        return $compiled;
    }

    /**
     * Randomly shuffle the contents of a passed array.
     */
    public function shuffle(array $array): array
    {
        if (is_array($array)) {
            shuffle($array);
        }

        return $array;
    }

    /**
     * Sorts / orders items of an array.
     */
    public function order(array $array, string $on, ?string $onSecondary = null): array
    {
        // Set the 'orderOn' and 'orderAscending', taking into account things like '-datepublish'.
        [$this->orderOn, $this->orderAscending] = $this->getSortOrder($on);

        // Set the secondary order, if any.
        if ($onSecondary) {
            [$this->orderOnSecondary, $this->orderAscendingSecondary] = $this->getSortOrder($onSecondary);
        } else {
            $this->orderOnSecondary = null;
            $this->orderAscendingSecondary = null;
        }

        uasort($array, function ($a, $b): void {
            $this->orderHelper($a, $b);
        });

        return $array;
    }

    /**
     * Get sorting order of name, stripping possible "DESC", "ASC", and also
     * return the sorting order.
     */
    private function getSortOrder(string $name = '-datepublish'): array
    {
        $parts = explode(' ', $name);
        $fieldName = $parts[0];
        $sort = 'ASC';
        if (isset($parts[1])) {
            $sort = $parts[1];
        }

        if ($fieldName[0] === '-') {
            $fieldName = mb_substr($fieldName, 1);
            $sort = 'DESC';
        }

        return [$fieldName, (mb_strtoupper($sort) === 'ASC')];
    }

    /**
     * Helper function for sorting an array of \Bolt\Legacy\Content.
     */
    private function orderHelper(Content $a, Content $b): int
    {
        $aVal = $a->getField($this->orderOn);
        $bVal = $b->getField($this->orderOn);

        // Check the primary sorting criterion.
        if ($aVal < $bVal) {
            return -$this->orderAscending;
        } elseif ($aVal > $bVal) {
            return $this->orderAscending;
        }
        // Primary criterion is the same. Use the secondary criterion, if it is set. Otherwise return 0.
        if (empty($this->orderOnSecondary)) {
            return 0;
        }

        $aVal = $a->getField($this->orderOnSecondary);
        $bVal = $b->getField($this->orderOnSecondary);

        if ($aVal < $bVal) {
            return -$this->orderAscendingSecondary;
        } elseif ($aVal > $bVal) {
            return $this->orderAscendingSecondary;
        }

        // both criteria are the same. Whatever!
        return 0;
    }
}
