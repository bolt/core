import { describe, expect, it } from 'vitest';
import { raw, slugify, strip, trim, uppercase } from '../../../assets/js/filters/string';

describe('slugify', () => {
    it('lowercases and joins words with the delimiter', () => {
        expect(slugify('Hello World')).toBe('hello-world');
    });

    it('transliterates accented and non-latin characters', () => {
        expect(slugify('Žluťoučký kůň')).toBe('zlutoucky-kun');
        expect(slugify('Größe')).toBe('grosse');
        expect(slugify('Ελλάδα')).toBe('ellada');
    });

    it('collapses runs of non-alphanumerics into a single delimiter', () => {
        expect(slugify('a  --  b')).toBe('a-b');
    });

    it('strips leading and trailing delimiters', () => {
        expect(slugify('  spaced  ')).toBe('spaced');
        expect(slugify('---edge---')).toBe('edge');
    });

    it('returns undefined for falsy input', () => {
        // The falsy guard means an empty string and 0 are indistinguishable
        // from "no value" — callers rely on this to skip rendering.
        expect(slugify('')).toBeUndefined();
        expect(slugify(0)).toBeUndefined();
        expect(slugify(null)).toBeUndefined();
        expect(slugify(undefined)).toBeUndefined();
        expect(slugify(false)).toBeUndefined();
    });

    it('coerces non-string input', () => {
        expect(slugify(42)).toBe('42');
    });

    it('stops transliterating when XRegExp is on the page', () => {
        // Carried over from the gist this came from: transliteration is skipped
        // when XRegExp is available, on the assumption it will handle unicode
        // instead. XRegExp is never bundled, so this branch is dead in practice
        // — accented characters are simply dropped if it ever were.
        const globals = globalThis as Record<string, unknown>;
        globals.XRegExp = {};
        try {
            expect(slugify('Žluťoučký kůň')).toBe('lu-ou-k-k');
        } finally {
            delete globals.XRegExp;
        }
    });
});

describe('strip', () => {
    it('removes a leading and trailing double quote', () => {
        expect(strip('"quoted"')).toBe('quoted');
    });

    it('removes the quote even when only one end has it', () => {
        expect(strip('"open')).toBe('open');
        expect(strip('close"')).toBe('close');
    });

    it('leaves inner quotes alone', () => {
        expect(strip('say "this" loudly')).toBe('say "this" loudly');
    });

    it('returns undefined for falsy input', () => {
        expect(strip('')).toBeUndefined();
        expect(strip(null)).toBeUndefined();
    });
});

describe('raw', () => {
    it('unescapes HTML entities', () => {
        expect(raw('Fish &amp; Chips')).toBe('Fish & Chips');
        expect(raw('&lt;fresh&gt;')).toBe('<fresh>');
        expect(raw('&quot;daily&quot;')).toBe('"daily"');
    });

    it('leaves unescaped text untouched', () => {
        expect(raw('plain text')).toBe('plain text');
    });

    it('returns undefined for falsy input', () => {
        expect(raw('')).toBeUndefined();
        expect(raw(null)).toBeUndefined();
    });
});

describe('uppercase', () => {
    it('uppercases the string', () => {
        expect(uppercase('bolt')).toBe('BOLT');
    });

    it('coerces non-string input rather than throwing', () => {
        expect(uppercase(42)).toBe('42');
    });

    it('returns undefined for falsy input', () => {
        expect(uppercase('')).toBeUndefined();
        expect(uppercase(null)).toBeUndefined();
    });
});

describe('trim', () => {
    it('leaves a string shorter than the limit untouched', () => {
        expect(trim('short', 50)).toBe('short');
    });

    it('truncates to the limit and appends an ellipsis', () => {
        // The ellipsis is one character, so the result is exactly `max` long.
        const result = trim('a'.repeat(60), 10) as string;
        expect(result).toBe('aaaaaaaaa…');
        expect(result).toHaveLength(10);
    });

    it('defaults the limit to 50', () => {
        expect(trim('a'.repeat(60))).toHaveLength(50);
        expect(trim('a'.repeat(50))).toHaveLength(50);
    });

    it('treats a null length as unset', () => {
        expect(trim('a'.repeat(60), null)).toHaveLength(50);
    });

    it('returns undefined only for null and undefined', () => {
        expect(trim(null)).toBeUndefined();
        expect(trim(undefined)).toBeUndefined();
    });

    it('passes an empty string through rather than dropping it', () => {
        expect(trim('')).toBe('');
    });

    it('passes non-strings through untouched', () => {
        // Deliberate: the original read `.length` straight off the argument, so a
        // number has no length, fails the comparison and is returned as-is for
        // Vue's own stringifier. Coercing would truncate numbers and turn
        // objects into "[object Object]".
        expect(trim(12345)).toBe(12345);
        const object = { a: 1 };
        expect(trim(object)).toBe(object);
    });
});
