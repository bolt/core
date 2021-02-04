<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Exception;

class FlagMaster
{
    /**
     * Converts string of (one) country code to emoji flag (string).
     * Makes correction for codes that have no corresponding flag.
     * Most flags have 2-letter code, but some have more (eg England=gbeng,
     * Scotland=gbsct, Wales=gbwls, etc.).
     *
     * @param string $code (one or more 2-letter codes)
     *
     * @throws \Exception
     */
    public static function emojiFlag($code): string
    {
        if (! is_string($code) || mb_strlen($code) < 2) {
            throw new Exception('Argument code must be non-empty string');
        }
        $code = mb_strtolower($code);
        $replacement = [
            'uk' => 'gb',
            'an' => 'nl',
            'ap' => 'un',
        ];
        if (array_key_exists($code, $replacement)) {
            $code = $replacement[$code];
        }

        return self::code2unicode($code);
    }

    /**
     * Converts country (or region) code to emoji flag. One flag only!
     *
     * @param string $code (2 or more letter code)
     *
     * @throws \Exception
     */
    private static function code2unicode($code): string
    {
        $arr = str_split($code);
        $str = '';
        foreach ($arr as $char) {
            $str .= self::enclosedUnicode($char);
        }

        return $str;
    }

    /**
     * Converts a character into enclosed unicode.
     *
     * @param string $char (one character)
     *
     * @throws \Exception
     */
    private static function enclosedUnicode($char): string
    {
        $arr = [
            'a' => '1F1E6',
            'b' => '1F1E7',
            'c' => '1F1E8',
            'd' => '1F1E9',
            'e' => '1F1EA',
            'f' => '1F1EB',
            'g' => '1F1EC',
            'h' => '1F1ED',
            'i' => '1F1EE',
            'j' => '1F1EF',
            'k' => '1F1F0',
            'l' => '1F1F1',
            'm' => '1F1F2',
            'n' => '1F1F3',
            'o' => '1F1F4',
            'p' => '1F1F5',
            'q' => '1F1F6',
            'r' => '1F1F7',
            's' => '1F1F8',
            't' => '1F1F9',
            'u' => '1F1FA',
            'v' => '1F1FB',
            'w' => '1F1FC',
            'x' => '1F1FD',
            'y' => '1F1FE',
            'z' => '1F1FF',
        ];
        $char = mb_strtolower($char);
        if (array_key_exists($char, $arr)) {
            return mb_convert_encoding('&#x' . $arr[$char] . ';', 'UTF-8', 'HTML-ENTITIES');
        }
        throw new Exception('Illegal value argument char');
    }
}
