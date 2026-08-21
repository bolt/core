import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import flatPickr from 'vue-flatpickr-component';
import DateField from '@/editor/Components/Date.vue';

/**
 * This spec drives the real flatpickr rather than a stub. fixRequired() reaches
 * into the DOM flatpickr builds — it targets `.editor--date.input`, the visible
 * alt-input, which only exists once the library has run — so a stub cannot
 * exercise it. flatpickr runs happily under jsdom.
 */

const defaultProps = {
    value: '2024-03-15 10:00:00',
    name: 'fields[publishedAt]',
    readonly: false,
    mode: 'date',
    form: 'editcontent',
    locale: 'en',
    labels: '',
    required: false,
    errormessage: 'Pick a date',
};

const mountDate = (props: Record<string, unknown> = {}) =>
    mount(DateField, { propsData: { ...defaultProps, ...props }, attachTo: document.body });

/** The hidden input flatpickr posts. */
const submitted = (wrapper: ReturnType<typeof mount>) => wrapper.find('input[data-input]');
/** The readable input flatpickr shows the formatted date in. */
const visible = (wrapper: ReturnType<typeof mount>) => wrapper.find('.editor--date.input');
const config = (wrapper: ReturnType<typeof mount>) =>
    wrapper.findComponent(flatPickr).props('config') as Record<string, unknown>;

const pick = async (wrapper: ReturnType<typeof mount>, date: string) => {
    const instance = (
        wrapper.findComponent(flatPickr).vm as unknown as {
            fp: { setDate: (date: string, triggerChange: boolean) => void };
        }
    ).fp;
    instance.setDate(date, true);
    await nextTick();
};

describe('EditorDate', () => {
    it('posts the value under the field name and form', async () => {
        const wrapper = mountDate();
        await nextTick();

        expect(submitted(wrapper).attributes('name')).toBe('fields[publishedAt]');
        expect(submitted(wrapper).attributes('form')).toBe('editcontent');
        expect(submitted(wrapper).attributes('data-errormessage')).toBe('Pick a date');
        expect((submitted(wrapper).element as HTMLInputElement).value).toBe('2024-03-15 10:00:00');
    });

    it('shows the date in a friendly format', async () => {
        const wrapper = mountDate();
        await nextTick();

        expect((visible(wrapper).element as HTMLInputElement).value).toBe('March 15, 2024');
    });

    it('takes a newly picked date', async () => {
        const wrapper = mountDate();
        await nextTick();

        await pick(wrapper, '2025-01-02 00:00:00');

        expect((submitted(wrapper).element as HTMLInputElement).value).toBe('2025-01-02 00:00:00');
        expect((visible(wrapper).element as HTMLInputElement).value).toBe('January 2, 2025');
    });

    it('renders a date picker without a time of day by default', async () => {
        const wrapper = mountDate();
        await nextTick();

        expect(config(wrapper).enableTime).toBe(false);
        expect(config(wrapper).altFormat).toBe('F j, Y');
    });

    it('adds the time of day in datetime mode', async () => {
        const wrapper = mountDate({ mode: 'datetime' });
        await nextTick();

        expect(config(wrapper).enableTime).toBe(true);
        expect(config(wrapper).altFormat).toBe('F j, Y - h:i K');
        expect((visible(wrapper).element as HTMLInputElement).value).toBe('March 15, 2024 - 10:00 AM');
    });

    it('wraps the picker so the toggle and clear buttons drive it', async () => {
        const wrapper = mountDate();
        await nextTick();

        expect(config(wrapper).wrap).toBe(true);
        expect(wrapper.find('button[data-toggle]').exists()).toBe(true);
        expect(wrapper.find('button[data-clear]').exists()).toBe(true);
    });

    it('labels the toggle and clear buttons for screen readers', async () => {
        const wrapper = mountDate();
        await nextTick();

        expect(wrapper.find('button[data-toggle]').attributes('aria-label')).toBe('Date picker');
        expect(wrapper.find('button[data-clear]').attributes('aria-label')).toBe('Reset date');
    });

    it('clears the date with the clear button', async () => {
        const wrapper = mountDate();
        await nextTick();

        await wrapper.find('button[data-clear]').trigger('click');

        expect((submitted(wrapper).element as HTMLInputElement).value).toBe('');
    });

    it('disables the field and its buttons when read-only', async () => {
        const wrapper = mountDate({ readonly: true });
        await nextTick();

        expect(visible(wrapper).attributes('disabled')).toBeDefined();
        expect(wrapper.find('button[data-toggle]').attributes('disabled')).toBeDefined();
        expect(wrapper.find('button[data-clear]').attributes('disabled')).toBeDefined();
        expect(wrapper.find('button[data-toggle]').classes()).toContain('btn-outline-secondary');
    });

    it('uses the default english locale without loading a locale file', async () => {
        const wrapper = mountDate({ locale: 'en' });
        await nextTick();

        expect(config(wrapper).locale).toBeUndefined();
    });

    it('never actually marks an emptied required field as required', async () => {
        // Pinning current behaviour, which is a bug. fixRequired() adds the
        // attribute when `this.val === ''`, but clearing the picker sets `val`
        // to null, so the strict comparison never matches and the field is left
        // unmarked — the browser will happily submit the form with it empty.
        // 37b95add reworks this hook.
        const wrapper = mountDate({ required: true });
        await nextTick();

        await pick(wrapper, '');

        expect(visible(wrapper).attributes('required')).toBeUndefined();
    });

    it('leaves an optional field unmarked', async () => {
        const wrapper = mountDate({ required: false });
        await nextTick();

        await pick(wrapper, '');

        expect(visible(wrapper).attributes('required')).toBeUndefined();
    });

    it.skip('loads the flatpickr locale for a non-english locale', () => {
        // Cannot run under Vite: created() resolves the locale file with a
        // template-literal `require()`, which has no meaning in an ES module and
        // throws "require is not defined". Unblocked by e286000a, which replaces
        // the dynamic require.
    });

    it.skip('does not surface the native validation popup on mount', () => {
        // Only passes after 37b95add. Today fixRequired() calls reportValidity()
        // from updated(), so an empty required field can flash the browser's
        // "please fill in this field" popup before the user has touched it.
    });
});
