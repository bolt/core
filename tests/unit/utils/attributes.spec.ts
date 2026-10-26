import { describe, expect, it } from 'vitest';
import { optionalAttribute } from '@/editor/utils/attributes';

describe('optionalAttribute', () => {
    it('turns false into undefined, which drops the attribute in Vue 2 and Vue 3 alike', () => {
        expect(optionalAttribute(false)).toBeUndefined();
    });

    it.each(['[a-z]+', '', 0, null, undefined])('leaves %j as it is', value => {
        expect(optionalAttribute(value)).toBe(value);
    });
});
