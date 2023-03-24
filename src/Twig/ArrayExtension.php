<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Bolt\Entity\Field\NumberField;
use Bolt\Utils\ContentHelper;
use Carbon\Carbon;
use Iterator;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
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
    /** @var ContentHelper */
    private $contentHelper;

    /** @var LocaleExtension */
    private $localeExtension;

    /** @var string */
    private $defaultLocale;
    
    /** @var RequestStack */
    private $requestStack;

    public function __construct(ContentHelper $contentHelper, LocaleExtension $localeExtension, string $defaultLocale, RequestStack $requestStack)
    {
        $this->contentHelper = $contentHelper;
        $this->localeExtension = $localeExtension;
        $this->defaultLocale = $defaultLocale;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        $env = ['needs_environment' => true];

        return [
            new TwigFilter('order', [$this, 'order'], $env),
            new TwigFilter('shuffle', [$this, 'shuffle']),
            new TwigFilter('length', [$this, 'length'], $env),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('paginate', [$this, 'paginate']),
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
    public function order(Environment $twig, $array, string $on = '-publishedAt', ?string $onSecondary = null, $locale = null): array
    {
        $array = $this->getArray($array);

        if (! $locale) {
            $locale = ! empty($this->localeExtension->getHtmlLang($twig)) ?
                $this->localeExtension->getHtmlLang($twig) : $this->defaultLocale;
        }

        // Set the 'orderOn' and 'orderAscending', taking into account things like '-publishedAt'.
        [$orderOn, $orderAscending] = self::getSortOrder($on);

        // Set the secondary order, if any.
        [$orderOnSecondary, $orderAscendingSecondary] = self::getSortOrder($onSecondary);

        try {
            uasort($array, function ($a, $b) use ($orderOn, $orderAscending, $orderOnSecondary, $orderAscendingSecondary, $locale): int {
                $check = $this->orderHelper($a, $b, $orderOn, $orderAscending, $locale);
                if ($check !== 0 || $orderOnSecondary !== '') {
                    return $check;
                }

                return $this->orderHelper($a, $b, $orderOnSecondary, $orderAscendingSecondary, $locale);
            });
        } catch (\Exception $e) {
            // If sorting failed, we don't sort..
        }

        return $array;
    }

    /**
     * Paginate filter results so you wont have random amounts of pages in random pages
     */
    public function paginate($array, int $pageSize = 10): Pagerfanta
    {
        if ($array instanceof Pagerfanta) {
            return $array;
        }
        $array = new Pagerfanta(new ArrayAdapter($array));
        $array->setMaxPerPage($pageSize);

        $currentPage = array_merge(
            $this->requestStack->getCurrentRequest()->get('_route_params'),
            $this->requestStack->getCurrentRequest()->query->all()
        );
        //Set the default page to 1 if the page is not set
        if (array_key_exists('page', $currentPage)) {
            $array->setCurrentPage((int) $currentPage["page"]);
        } else {
            $array->setCurrentPage(1);
        }

        $array->getNbResults();
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
    private function orderHelper(Content $a, Content $b, string $orderOn, bool $orderAscending, string $locale): int
    {
        $aVal = mb_strtolower($this->contentHelper->get($a, sprintf('{%s}', $orderOn), $locale));
        $bVal = mb_strtolower($this->contentHelper->get($b, sprintf('{%s}', $orderOn), $locale));

        // If the values look like dates, convert them to proper date objects.
        if ($a->getDefinition()->get('fields')->get($orderOn, null) &&
            $a->getDefinition()->get('fields')->get($orderOn)->get('type') === NumberField::TYPE) {
            $aVal = (int) $aVal;
            $bVal = (int) $bVal;
        } elseif (strtotime($aVal) && strtotime($bVal)) {
            $aVal = Carbon::createFromTimestamp(strtotime($aVal));
            $bVal = Carbon::createFromTimestamp(strtotime($bVal));
        }

        // Check the primary sorting criterion.
        if ($orderAscending) {
            return $aVal <=> $bVal;
        }

        return $bVal <=> $aVal;
    }

    private function getArray($array)
    {
        if ($array instanceof Pagerfanta) {
            $array = (array) $array->getCurrentPageResults();
        } elseif ($array instanceof Iterator) {
            // Special edge-case for "|filter. See #2601
            $array = iterator_to_array($array);
        } elseif (! is_array($array) && is_iterable($array)) {
            $array = (array) $array;
        }

        return $array;
    }
}
