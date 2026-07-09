import { useFieldValue } from '@/editor/composables/useFieldValue';
import { describe, it, expect } from 'vitest';

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

    it('unescapes HTML entities into rawVal', () => {
        const { rawVal } = useFieldValue('Fish &amp; Chips &lt;fresh&gt; &quot;daily&quot;');
        expect(rawVal.value).toBe('Fish & Chips <fresh> "daily"');
    });

    it('handles an undefined value', () => {
        const { val, rawVal } = useFieldValue(undefined);
        expect(val.value).toBeUndefined();
        expect(rawVal.value).toBe('');
    });

    it('handles a numeric value', () => {
        const { val, rawVal } = useFieldValue(42);
        expect(val.value).toBe(42);
        expect(rawVal.value).toBe('42');
    });
});
