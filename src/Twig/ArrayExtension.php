<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Bolt\Utils\ContentHelper;
use Pagerfanta\Pagerfanta;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Bolt specific Twig functions and filters that provide array manipulation.
 *
 * @internal
 */
final class ArrayExtension extends AbstractExtension
{
    /** @var ContentHelper */
    private $contentHelper;

    public function __construct(ContentHelper $contentHelper)
    {
        $this->contentHelper = $contentHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        $env = ['needs_environment' => true];

        return [
            new TwigFilter('order', [$this, 'order']),
            new TwigFilter('shuffle', [$this, 'shuffle']),
            new TwigFilter('length', [$this, 'length'], $env),
        ];
    }

    /**
     * Randomly shuffle the contents of a passed array.
     */
    public function shuffle($array)
    {
        $array = $this->getArray($array);

        if (is_array($array)) {
            shuffle($array);
        }

        return $array;
    }

    /**
     * Returns the length of a variable.
     * Overrides the default Twig |length filter
     * for accurate results with paginated content
     */
    public function length(Environment $env, $thing)
    {
        return twig_length_filter($env, $this->getArray($thing));
    }

    /**
     * Sorts / orders items of an array.
     */
    public function order($array, string $on = '-publishedAt', ?string $onSecondary = null): array
    {
        if ($array instanceof Pagerfanta) {
            $array = (array) $array->getCurrentPageResults();
        } elseif (! is_array($array) && is_iterable($array)) {
            $array = (array) $array;
        }

        // Set the 'orderOn' and 'orderAscending', taking into account things like '-publishedAt'.
        [$orderOn, $orderAscending] = self::getSortOrder($on);

        // Set the secondary order, if any.
        [$orderOnSecondary, $orderAscendingSecondary] = self::getSortOrder($onSecondary);

        uasort($array, function ($a, $b) use ($orderOn, $orderAscending, $orderOnSecondary, $orderAscendingSecondary): int {
            $check = $this->orderHelper($a, $b, $orderOn, $orderAscending);
            if ($check !== 0 || $orderOnSecondary !== '') {
                return $check;
            }

            return $this->orderHelper($a, $b, $orderOnSecondary, $orderAscendingSecondary);
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
    private function orderHelper(Content $a, Content $b, string $orderOn, bool $orderAscending): int
    {
        $aVal = $this->contentHelper->get($a, sprintf('{%s}', $orderOn));
        $bVal = $this->contentHelper->get($b, sprintf('{%s}', $orderOn));

        // Check the primary sorting criterion.
        if ($orderAscending) {
            return $aVal <=> $bVal;
        }

        return $bVal <=> $aVal;
    }

    private function getArray($array)
    {
        if ($array instanceof Pagerfanta) {
            return (array) $array->getCurrentPageResults();
        }

        return $array;
    }
}
