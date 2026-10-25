import { describe, expect, it } from 'vitest';
import { useFieldValue } from '@/editor/composables/useFieldValue';

/**
 * The shared field-value composable. Every editor input uses it to get `val`
 * (the raw prop) and `rawVal` (the same value with html entities decoded).
 * It replaces the `mixins/value` mixin of the Vue 2 components.
 */
describe('useFieldValue', () => {
    it('initializes val from the value', () => {
        const { val } = useFieldValue('hello');

        expect(val.value).toBe('hello');
    });

    it('keeps val and rawVal independently writable', () => {
        const { val, rawVal } = useFieldValue('original');

        val.value = 'edited';
        rawVal.value = 'raw-edited';

        expect(val.value).toBe('edited');
        expect(rawVal.value).toBe('raw-edited');
    });

    it('decodes html entities into rawVal, leaving val alone', () => {
        const { val, rawVal } = useFieldValue('Fish &amp; Chips &lt;fresh&gt; &quot;daily&quot;');

        expect(rawVal.value).toBe('Fish & Chips <fresh> "daily"');
        expect(val.value).toBe('Fish &amp; Chips &lt;fresh&gt; &quot;daily&quot;');
    });

    it('leaves text without entities identical in both', () => {
        const { val, rawVal } = useFieldValue('plain text');

        expect(val.value).toBe(rawVal.value);
    });

    it('handles an undefined value', () => {
        // The mixin assigned the prop straight to innerHTML, so a missing value
        // came back as the literal word "undefined". The composable does not.
        const { val, rawVal } = useFieldValue(undefined);

        expect(val.value).toBeUndefined();
        expect(rawVal.value).toBe('');
    });

    it('handles an empty value', () => {
        const { val, rawVal } = useFieldValue('');

        expect(val.value).toBe('');
        expect(rawVal.value).toBe('');
    });

    it('keeps a numeric value numeric in val and stringifies it into rawVal', () => {
        const { val, rawVal } = useFieldValue(42);

        expect(val.value).toBe(42);
        expect(rawVal.value).toBe('42');
    });

    it('gives each call its own state', () => {
        const first = useFieldValue('one');
        const second = useFieldValue('two');

        first.val.value = 'changed';

        expect(second.val.value).toBe('two');
    });
});
