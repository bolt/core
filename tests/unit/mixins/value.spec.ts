import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import value from '@/editor/mixins/value';

/**
 * The shared field-value mixin. Every editor input mixes this in to get `val`
 * (the raw prop) and `rawVal` (the same value with html entities decoded).
 *
 * On refactor/vue3-composition-api this becomes a `useFieldValue` composable, so
 * this spec is replaced rather than ported at the Vue 3 upgrade.
 */
const host = {
    name: 'ValueMixinHost',
    mixins: [value],
    props: { value: { default: undefined } },
    template: '<div><span class="val">{{ val }}</span><span class="raw">{{ rawVal }}</span></div>',
};

const mountHost = (propValue: unknown) => mount(host, { propsData: { value: propValue } });
const val = (wrapper: ReturnType<typeof mount>) => wrapper.find('.val').text();
const rawVal = (wrapper: ReturnType<typeof mount>) => wrapper.find('.raw').text();

describe('editor value mixin', () => {
    it('starts empty before the component is mounted', () => {
        // Both fields are declared as null in data() and only filled in
        // mounted(), so the very first render shows nothing.
        const wrapper = mountHost('hello');

        expect(val(wrapper)).toBe('');
        expect(rawVal(wrapper)).toBe('');
    });

    it('copies the value across once mounted', async () => {
        const wrapper = mountHost('hello');
        await nextTick();

        expect(val(wrapper)).toBe('hello');
        expect(rawVal(wrapper)).toBe('hello');
    });

    it('decodes html entities into rawVal, leaving val alone', async () => {
        const wrapper = mountHost('Fish &amp; Chips &lt;fresh&gt; &quot;daily&quot;');
        await nextTick();

        expect(rawVal(wrapper)).toBe('Fish & Chips <fresh> "daily"');
        expect(val(wrapper)).toBe('Fish &amp; Chips &lt;fresh&gt; &quot;daily&quot;');
    });

    it('leaves text without entities identical in both', async () => {
        const wrapper = mountHost('plain text');
        await nextTick();

        expect(val(wrapper)).toBe(rawVal(wrapper));
    });

    it('turns a missing value into the literal string "undefined" in rawVal', async () => {
        // Pinning current behaviour, which is a bug worth knowing about: the
        // decode step assigns the prop straight to `innerHTML`, so `undefined`
        // is stringified by the DOM and read back as the word "undefined".
        // Fields bound to rawVal — Text, Textarea — would show it if Twig ever
        // omitted the value attribute.
        const wrapper = mountHost(undefined);
        await nextTick();

        expect(val(wrapper)).toBe('');
        expect(rawVal(wrapper)).toBe('undefined');
    });

    it('handles an empty value', async () => {
        const wrapper = mountHost('');
        await nextTick();

        expect(val(wrapper)).toBe('');
        expect(rawVal(wrapper)).toBe('');
    });

    it('stringifies a numeric value into rawVal', async () => {
        const wrapper = mountHost(42);
        await nextTick();

        expect(val(wrapper)).toBe('42');
        expect(rawVal(wrapper)).toBe('42');
    });

    it('gives each instance its own state', async () => {
        const first = mountHost('one');
        const second = mountHost('two');
        await nextTick();

        expect(val(first)).toBe('one');
        expect(val(second)).toBe('two');
    });
});
