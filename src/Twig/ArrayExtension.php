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
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('order', [$this, 'order']),
            new TwigFilter('shuffle', [$this, 'shuffle']),
        ];
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
    public static function order(array $array, string $on = '-datepublish', ?string $onSecondary = null): array
    {
        // Set the 'orderOn' and 'orderAscending', taking into account things like '-datepublish'.
        [$orderOn, $orderAscending] = self::getSortOrder($on);

        // Set the secondary order, if any.
        [$orderOnSecondary, $orderAscendingSecondary] = self::getSortOrder($onSecondary);

        uasort($array, function ($a, $b) use ($orderOn, $orderAscending, $orderOnSecondary, $orderAscendingSecondary): int {
            $check = self::orderHelper($a, $b, $orderOn, $orderAscending);
            if ($check !== 0 || $orderOnSecondary !== '') {
                return $check;
            }
            return self::orderHelper($a, $b, $orderOnSecondary, $orderAscendingSecondary);
        });

        return $array;
    }

    /**
     * Get sorting order of name, stripping possible "DESC", "ASC", and also
     * return the sorting order.
     */
    private static function getSortOrder(?string $name): array
    {
        if ($name === null) {
            return ['', true];
        }

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
     * Helper function for sorting an array of Content.
     */
    private static function orderHelper(Content $a, Content $b, string $orderOn, bool $orderAscending): int
    {
        $aVal = $a->getField($orderOn);
        $bVal = $b->getField($orderOn);

        // Check the primary sorting criterion.
        if ($orderAscending) {
            return $aVal <=> $bVal;
        }
        return $bVal <=> $aVal;
    }
}
