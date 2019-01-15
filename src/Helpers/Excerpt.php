<?php

declare(strict_types=1);

namespace Bolt\Helpers;

use Bolt\Entity\Content;
use Bolt\Entity\Field\Excerptable;
use Bolt\Utils\Html;

class Excerpt
{
    /** @var Content|string */
    protected $content;

    /** @var string */
    protected $title;

    /**
     * Constructor.
     *
     * @param Content|string $content
     */
    public function __construct($content, ?string $title = null)
    {
        $this->content = $content;
        $this->title = $title;
    }

    /**
     * Get the excerpt of a given piece of text.
     *
     * @param string|array|null $focus
     */
    public function getExcerpt(int $length = 200, bool $includeTitle = false, $focus = null): string
    {
        $title = null;
        $excerpt = '';

        if ($includeTitle && $this->content->magicTitle() !== null) {
            $title = Html::trimText($this->content->magicTitle(), $length);
            $length -= mb_strlen($title);
        }

        if ($this->content instanceof Content) {
            $skipFields = $this->content->magicTitleFields();

            foreach ($this->content->getFields() as $field) {
                if (in_array($field->getName(), $skipFields, true) === false && $field instanceof Excerptable) {
                    $excerpt .= $field->__toString();
                }
            }
        } else {
            $excerpt = $this->content;
        }

        $excerpt = str_replace('>', '> ', $excerpt);

        if ($focus) {
            $excerpt = $this->extractRelevant($focus, $excerpt, $length);
        } else {
            $excerpt = Html::trimText($excerpt, $length);
        }

        if (! empty($title)) {
            $excerpt = '<strong>' . $title . '</strong> ' . '<span>' . $excerpt . '</span>';
        }

        return trim($excerpt);
    }

    /**
     * Find the locations of each of the words.
     * Nothing exciting here. The array_unique is required, unless you decide
     * to make the words unique before passing in.
     */
    private function extractLocations(array $words, string $fulltext): array
    {
        $locations = [];
        foreach ($words as $word) {
            $wordLen = mb_strlen($word);
            $loc = mb_stripos($fulltext, $word);
            while ($loc !== false) {
                $locations[] = $loc;
                $loc = mb_stripos($fulltext, $word, $loc + $wordLen);
            }
        }
        $locations = array_unique($locations);
        sort($locations);

        return $locations;
    }

    /**
     * Work out which is the most relevant portion to display
     * This is done by looping over each match and finding the smallest distance between two found
     * strings. The idea being that the closer the terms are the better match the snippet would be.
     * When checking for matches we only change the location if there is a better match.
     * The only exception is where we have only two matches in which case we just take the
     * first as will be equally distant.
     */
    private function determineSnipLocation(array $locations, int $prevCount): int
    {
        // If we only have 1 match we don't actually do the for loop so set to the first
        $startPos = (int) reset($locations);
        $locCount = count($locations);
        $smallestDiff = PHP_INT_MAX;

        // If we only have 2, skip as it's probably equally relevant
        if ($locCount > 2) {
            // skip the first as we check 1 behind
            for ($i = 1; $i < $locCount; ++$i) {
                if ($i === $locCount - 1) { // at the end
                    $diff = $locations[$i] - $locations[$i - 1];
                } else {
                    $diff = $locations[$i + 1] - $locations[$i];
                }

                if ($smallestDiff > $diff) {
                    $smallestDiff = $diff;
                    $startPos = $locations[$i];
                }
            }
        }

        return $startPos > $prevCount ? $startPos - $prevCount : 0;
    }

    /**
     * Center on, and highlight search terms in excerpts.
     *
     * @see: http://www.boyter.org/2013/04/building-a-search-result-extract-generator-in-php/
     *
     * @param string|array $words
     */
    private function extractRelevant($words, string $fulltext, int $relLength = 300): string
    {
        $fulltext = strip_tags($fulltext);

        if (! is_array($words)) {
            $words = explode(' ', $words);
        }

        // 1/6 ratio on prevcount tends to work pretty well and puts the terms
        // in the middle of the extract
        $prevCount = (int) floor($relLength / 6);

        $indicator = '…';

        $textlength = mb_strlen($fulltext);
        if ($textlength <= $relLength) {
            return $fulltext;
        }

        $locations = $this->extractLocations($words, $fulltext);
        $startPos = $this->determineSnipLocation($locations, $prevCount);

        // if we are going to snip too much...
        if ($textlength - $startPos < $relLength) {
            $startPos -= (int) round(($textlength - $startPos) / 2);
        }

        $relText = mb_substr($fulltext, $startPos, $relLength);

        // check to ensure we don't snip the last word if that's the match
        if ($startPos + $relLength < $textlength) {
            $relText = mb_substr($relText, 0, mb_strrpos($relText, ' ')) . $indicator; // remove last word
        }

        // If we trimmed from the front add '…'
        if ($startPos !== 0) {
            $relText = $indicator . mb_substr($relText, mb_strpos($relText, ' ') + 1); // remove first word
        }

        // Highlight the words, using the `<mark>` tag.
        foreach ($words as $word) {
            if ($word) {
                $relText = preg_replace('/\b(' . preg_quote($word, '/') . ')\b/i', '<mark>$1</mark>', $relText);
            }
        }

        return $relText;
    }
}
