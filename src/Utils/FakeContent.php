<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Faker\Provider\Lorem;

class FakeContent extends Lorem
{
    public static function generateMarkdown($loops = 5): string
    {
        $parts = [];
        $last = 'list';

        do {
            if (self::randomDigit() > 3) {
                $parts[] = self::markdownParagraphs(($loops * 2) - 2);
                $last = 'paragraphs';
            }

            if (self::randomDigit() > 8) {
                $parts[] = self::markdownBlockquote();
                $last = 'blockquote';
            }

            if (self::randomDigit() > 8 && $last !== 'list') {
                $parts[] = self::markdownBulletedList();
                $last = 'list';
            }

            if (self::randomDigit() > 8 && $last !== 'list') {
                $parts[] = self::markdownOrderedList();
                $last = 'list';
            }
        } while ($loops-- > 0 || count($parts) < 3);

        return implode("\n", $parts);
    }

    public static function generateHTML($loops = 5): string
    {
        $res = self::generateMarkdown($loops);

        $markdown = new Markdown();

        return $markdown->parse($res);
    }

    private static function markdownParagraphs(int $nbSentences = 3, bool $variableNbSentences = true): string
    {
        return self::paragraph($nbSentences, $variableNbSentences) . "\n";
    }

    private static function markdownBulletedList(int $nbSentences = 3): string
    {
        $res = '';

        foreach (self::sentences($nbSentences) as $element) {
            $res .= '* ' . $element . "\n";
        }

        return $res;
    }

    private static function markdownOrderedList(int $nbSentences = 3): string
    {
        $res = '';

        foreach (self::sentences($nbSentences) as $key => $element) {
            $res .= ($key + 1) . '. ' . $element . "\n";
        }

        return $res;
    }

    private static function markdownBlockquote(int $nbSentences = 3): string
    {
        $res = '';

        foreach (self::sentences($nbSentences) as $element) {
            $res .= '> ' . $element . "\n";
        }

        return $res;
    }
}
