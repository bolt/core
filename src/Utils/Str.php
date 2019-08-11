<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Cocur\Slugify\Slugify;

class Str extends \Bolt\Common\Str
{
    /**
     * We use Slugify as a Singleton because Slugify::create() is quite heavy
     *
     * @var Slugify
     */
    private static $slugifyInstance = null;

    /** @var Slugify[] */
    private static $slugifySafeInstances = [];

    /**
     * Returns a "safe" version of the given string - basically only US-ASCII and
     * numbers. Needed because filenames and titles and such, can't use all characters.
     */
    public static function makeSafe(string $str, bool $strict = false, string $extrachars = ''): string
    {
        $str = str_replace('&amp;', '', $str);

        $slugify = self::getSafeSlugify($strict, $extrachars);
        $str = $slugify->slugify($str, '');

        if ($strict) {
            $str = str_replace(' ', '-', $str);
        }

        return $str;
    }

    public static function slug(string $str, $options = null): string
    {
        return self::getSlugify()->slugify($str, $options);
    }

    /**
     * Add 'soft hyphens' &shy; to a string, so that it won't break layout in HTML when
     * using strings without spaces or dashes. Only breaks in long (> 19 chars) words.
     */
    public static function shyphenate(string $str): string
    {
        $res = preg_match_all('/([a-z0-9]{19,})/i', $str, $matches);

        if ($res) {
            foreach ($matches[1] as $match) {
                $str = str_replace($match, wordwrap($match, 10, '&shy;', true), $str);
            }
        }

        return $str;
    }

    private static function getSlugify(): Slugify
    {
        if (self::$slugifyInstance === null) {
            self::$slugifyInstance = Slugify::create();
        }
        return self::$slugifyInstance;
    }

    private static function getSafeSlugify(bool $strict = false, string $extrachars = ''): Slugify
    {
        $key = $strict ? 'strict_' : '' . $extrachars;

        if (empty(self::$slugifySafeInstances[$key]) === true) {
            $delim = '/';
            if ($extrachars !== '') {
                $extrachars = preg_quote($extrachars, $delim);
            }
            if ($strict) {
                $slugify = Slugify::create([
                    'regexp' => '/[^a-z0-9_' . $extrachars . ' -]+/',
                ]);
            } else {
                // Allow Uppercase and don't convert spaces to dashes
                $slugify = Slugify::create([
                    'regexp' => '/[^a-zA-Z0-9_.,' . $extrachars . ' -]+/',
                    'lowercase' => false,
                ]);
            }

            self::$slugifySafeInstances[$key] = $slugify;
        }

        return self::$slugifySafeInstances[$key];
    }

    public static function generatePassword($length = 12)
    {
        // The "pool" of potential characters contains special characters, but
        // with less frequency than 'a-z' and '0-9'.
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' .
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' .
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' .
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789' .
            '-=~!@#$%^&*()_+,./<>?;:[]{}\|';

        $str = '';
        $max = mb_strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, $max)];
        }

        return $str;
    }
}
