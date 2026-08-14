export function slugify(string: unknown) {
    if (string) {
        // based on https://gist.github.com/sgmurphy/3095196
        let slug = String(string);
        const opt = {
            delimiter: '-',
            lowercase: true,
            // XRegExp is never bundled, so this is always true. Kept as-is from
            // the original gist rather than silently changing the behaviour;
            // reading it off globalThis is what the bare identifier did.
            transliterate: typeof (globalThis as Record<string, unknown>).XRegExp === 'undefined' ? true : false,
        };
        const char_map: Record<string, string> = {
            // Latin
            À: 'A',
            Á: 'A',
            Â: 'A',
            Ã: 'A',
            Ä: 'A',
            Å: 'A',
            Ă: 'A',
            Æ: 'AE',
            Ç: 'C',
            È: 'E',
            É: 'E',
            Ê: 'E',
            Ë: 'E',
            Ì: 'I',
            Í: 'I',
            Î: 'I',
            Ï: 'I',
            Ð: 'D',
            Ñ: 'N',
            Ò: 'O',
            Ó: 'O',
            Ô: 'O',
            Õ: 'O',
            Ö: 'O',
            Ő: 'O',
            Ø: 'O',
            Ș: 'S',
            Ț: 'T',
            Ù: 'U',
            Ú: 'U',
            Û: 'U',
            Ü: 'U',
            Ű: 'U',
            Ý: 'Y',
            Þ: 'TH',
            ß: 'ss',
            à: 'a',
            á: 'a',
            â: 'a',
            ã: 'a',
            ä: 'a',
            å: 'a',
            ă: 'a',
            æ: 'ae',
            ç: 'c',
            è: 'e',
            é: 'e',
            ê: 'e',
            ë: 'e',
            ì: 'i',
            í: 'i',
            î: 'i',
            ï: 'i',
            ð: 'd',
            ñ: 'n',
            ò: 'o',
            ó: 'o',
            ô: 'o',
            õ: 'o',
            ö: 'o',
            ő: 'o',
            ø: 'o',
            ș: 's',
            ț: 't',
            ù: 'u',
            ú: 'u',
            û: 'u',
            ü: 'u',
            ű: 'u',
            ý: 'y',
            þ: 'th',
            ÿ: 'y',

            // Latin symbols
            '©': '(c)',

            // Greek
            Α: 'A',
            Β: 'B',
            Γ: 'G',
            Δ: 'D',
            Ε: 'E',
            Ζ: 'Z',
            Η: 'H',
            Θ: '8',
            Ι: 'I',
            Κ: 'K',
            Λ: 'L',
            Μ: 'M',
            Ν: 'N',
            Ξ: '3',
            Ο: 'O',
            Π: 'P',
            Ρ: 'R',
            Σ: 'S',
            Τ: 'T',
            Υ: 'Y',
            Φ: 'F',
            Χ: 'X',
            Ψ: 'PS',
            Ω: 'W',
            Ά: 'A',
            Έ: 'E',
            Ί: 'I',
            Ό: 'O',
            Ύ: 'Y',
            Ή: 'H',
            Ώ: 'W',
            Ϊ: 'I',
            Ϋ: 'Y',
            α: 'a',
            β: 'b',
            γ: 'g',
            δ: 'd',
            ε: 'e',
            ζ: 'z',
            η: 'h',
            θ: '8',
            ι: 'i',
            κ: 'k',
            λ: 'l',
            μ: 'm',
            ν: 'n',
            ξ: '3',
            ο: 'o',
            π: 'p',
            ρ: 'r',
            σ: 's',
            τ: 't',
            υ: 'y',
            φ: 'f',
            χ: 'x',
            ψ: 'ps',
            ω: 'w',
            ά: 'a',
            έ: 'e',
            ί: 'i',
            ό: 'o',
            ύ: 'y',
            ή: 'h',
            ώ: 'w',
            ς: 's',
            ϊ: 'i',
            ΰ: 'y',
            ϋ: 'y',
            ΐ: 'i',

            // Turkish
            Ş: 'S',
            İ: 'I',
            Ğ: 'G',
            ş: 's',
            ı: 'i',
            ğ: 'g',

            // Russian
            А: 'A',
            Б: 'B',
            В: 'V',
            Г: 'G',
            Д: 'D',
            Е: 'E',
            Ё: 'Yo',
            Ж: 'Zh',
            З: 'Z',
            И: 'I',
            Й: 'J',
            К: 'K',
            Л: 'L',
            М: 'M',
            Н: 'N',
            О: 'O',
            П: 'P',
            Р: 'R',
            С: 'S',
            Т: 'T',
            У: 'U',
            Ф: 'F',
            Х: 'H',
            Ц: 'C',
            Ч: 'Ch',
            Ш: 'Sh',
            Щ: 'Sh',
            Ъ: '',
            Ы: 'Y',
            Ь: '',
            Э: 'E',
            Ю: 'Yu',
            Я: 'Ya',
            а: 'a',
            б: 'b',
            в: 'v',
            г: 'g',
            д: 'd',
            е: 'e',
            ё: 'yo',
            ж: 'zh',
            з: 'z',
            и: 'i',
            й: 'j',
            к: 'k',
            л: 'l',
            м: 'm',
            н: 'n',
            о: 'o',
            п: 'p',
            р: 'r',
            с: 's',
            т: 't',
            у: 'u',
            ф: 'f',
            х: 'h',
            ц: 'c',
            ч: 'ch',
            ш: 'sh',
            щ: 'sh',
            ъ: '',
            ы: 'y',
            ь: '',
            э: 'e',
            ю: 'yu',
            я: 'ya',

            // Ukrainian
            Є: 'Ye',
            І: 'I',
            Ї: 'Yi',
            Ґ: 'G',
            є: 'ye',
            і: 'i',
            ї: 'yi',
            ґ: 'g',

            // Czech
            Č: 'C',
            Ď: 'D',
            Ě: 'E',
            Ň: 'N',
            Ř: 'R',
            Š: 'S',
            Ť: 'T',
            Ů: 'U',
            Ž: 'Z',
            Ľ: 'L',
            č: 'c',
            ď: 'd',
            ě: 'e',
            ň: 'n',
            ř: 'r',
            š: 's',
            ť: 't',
            ů: 'u',
            ž: 'z',
            ľ: 'l',

            // Polish
            Ą: 'A',
            Ć: 'C',
            Ę: 'e',
            Ł: 'L',
            Ń: 'N',
            Ś: 'S',
            Ź: 'Z',
            Ż: 'Z',
            ą: 'a',
            ć: 'c',
            ę: 'e',
            ł: 'l',
            ń: 'n',
            ś: 's',
            ź: 'z',
            ż: 'z',

            // Latvian
            Ā: 'A',
            Ē: 'E',
            Ģ: 'G',
            Ī: 'i',
            Ķ: 'k',
            Ļ: 'L',
            Ņ: 'N',
            Ū: 'u',
            ā: 'a',
            ē: 'e',
            ģ: 'g',
            ī: 'i',
            ķ: 'k',
            ļ: 'l',
            ņ: 'n',
            ū: 'u',
        };

        if (opt.transliterate) {
            for (const k in char_map) {
                slug = slug.replace(RegExp(k, 'g'), char_map[k]);
            }
        }

        // Replace non-alphanumeric characters with our delimiter
        const alnum = RegExp('[^a-z0-9]+', 'ig');
        slug = slug.replace(alnum, opt.delimiter);

        // Remove duplicate delimiters
        slug = slug.replace(RegExp('[' + opt.delimiter + ']{2,}', 'g'), opt.delimiter);

        // Remove delimiter from ends
        slug = slug.replace(RegExp('(^' + opt.delimiter + '|' + opt.delimiter + '$)', 'g'), '');

        slug = opt.lowercase ? slug.toLowerCase() : slug;
        return slug.replace(/[^\w-]+/g, '');
    }

    return undefined;
}

export function strip(string: unknown) {
    if (string) {
        return String(string).replace(/(^")|("$)/g, '');
    }

    return undefined;
}

export function raw(string: unknown) {
    if (string) {
        const node = document.createElement('textarea');
        node.innerHTML = String(string);
        return node.value;
    }

    return undefined;
}

export function uppercase(string: unknown) {
    if (string) return String(string).toUpperCase();

    return undefined;
}

export function trim(string: unknown, length?: number | null) {
    if (string === null || string === undefined) {
        return undefined;
    }

    // `?? 50` rather than a default parameter: the original used `length ==
    // undefined`, which also catches null, and a default parameter would not.
    const max = length ?? 50;
    // Deliberately not String()-coerced. The original read .length straight off
    // the argument, so a non-string (a number, an object) had an undefined
    // length, failed the comparison and was returned untouched for Vue's own
    // stringifier to render. Coercing here would truncate numbers and turn
    // objects into "[object Object]".
    const str = string as string;

    return str.length > max ? str.substring(0, max - 1) + '…' : str;
}
