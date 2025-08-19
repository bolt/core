<?php

declare(strict_types=1);

namespace Bolt\Utils;

use Bolt\Common\Str;

class Html
{
    /**
     * Trim text to a given length.
     *
     * @param string $str String to trim
     * @param int $desiredLength Target string length
     * @param bool $hellip Add dots when the string is too long
     * @param int $cutOffCap Maximum difference between string length when removing words
     *
     * @return string Trimmed string
     */
    public static function trimText(string $str, int $desiredLength, bool $hellip = true, int $cutOffCap = 3): string
    {
        if ($hellip) {
            $ellipseStr = '…';
            $newLength = $desiredLength - 1;
        } else {
            $ellipseStr = '';
            $newLength = $desiredLength;
        }

        $str = Str::cleanWhitespace(strip_tags($str));

        $str = filter_var($str, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);

        if (mb_strlen($str) > $desiredLength) {
            $nextChar = mb_substr($str, $newLength, 1);
            $str = mb_substr($str, 0, $newLength);
            $lastSpace = mb_strrpos($str, ' ');
            if ($nextChar !== ' ' && $lastSpace !== false) {
                // Check for too long cutoff
                if (mb_strlen($str) - $lastSpace >= $cutOffCap) {
                    // Trim the ellipse, as we do not want a space now
                    return $str . Str::cleanWhitespace($ellipseStr);
                }
                $str = mb_substr($str, 0, $lastSpace);
            }
            $str .= $ellipseStr;
        }

        return $str;
    }

    /**
     * Transforms plain text to HTML. Plot twist: text between backticks (`) is
     * wrapped in a <tt> element.
     *
     * @param string $str Input string. Treated as plain text.
     *
     * @return string The resulting HTML
     */
    public static function decorateTT($str): string
    {
        $str = htmlspecialchars($str, ENT_QUOTES);

        return preg_replace('/`([^`]*)`/', '<tt>\\1</tt>', $str);
    }

    /**
     * Check if a given string looks like it could be a URL, with or without the protocol.
     *
     * @see https://mathiasbynens.be/demo/url-regex
     */
    public static function isURL(string $str): bool
    {
        $pattern = '~^(?:\b[a-z\d.-]+://[^<>\s]+|\b(?:(?:(?:[^\s!@#$%^&*()_=+[\]{}\|;:\'",.<>/?]+)\.)+(?:ac|ad|aero|ae|af|ag|ai|al|am|an|ao|aq|arpa|ar|asia|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|biz|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|cat|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|coop|com|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|info|int|in|io|iq|ir|is|it|je|jm|jobs|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mobi|mo|mp|mq|mr|ms|mt|museum|mu|mv|mw|mx|my|mz|name|na|nc|net|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pro|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tel|tf|tg|th|tj|tk|tl|tm|tn|to|tp|travel|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|xn--0zwm56d|xn--11b5bs3a9aj6g|xn--80akhbyknj4f|xn--9t4b11yi5a|xn--deba0ad|xn--g6w251d|xn--hgbk6aj7f53bba|xn--hlcj6aya9esc7a|xn--jxalpdlp|xn--kgbechtv|xn--zckzah|ye|yt|yu|za|zm|zw)|(?:(?:[0-9]|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])\.){3}(?:[0-9]|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5]))(?:[;/][^#?<>\s]*)?(?:\?[^#<>\s]*)?(?:#[^<>\s]*)?(?!\w))$~iS';

        // Special case that isn't caught by this regex: 'http://' or 'https://' without a domain.
        if (preg_match('~^https?://$~i', $str)) {
            return false;
        }

        return (bool) preg_match($pattern, $str . '/');
    }

    /**
     * Add 'http://' to a link, if it has no protocol already.
     */
    public static function addScheme(string $url, string $scheme = 'http://'): string
    {
        return parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
    }

    public static function makeAbsoluteLink(string $link): string
    {
        if (mb_strpos($link, '://') !== false || mb_substr($link, 0, 2) === '//') {
            return $link;
        }

        return Str::ensureStartsWith($link, '/');
    }
}
