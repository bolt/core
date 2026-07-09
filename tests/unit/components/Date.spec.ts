import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import DateField from '@/editor/Components/Date.vue';
import flatPickr from 'vue-flatpickr-component';
import { describe, it, expect, afterEach, vi } from 'vitest';

describe('EditorDate Component', () => {
    let wrapper: VueWrapper<ComponentPublicInstance> | null = null;

    const defaultProps = {
        value: '2026-07-06 10:00:00',
        name: 'fields[publishdate]',
        readonly: false,
        mode: 'date',
        form: 'editcontent',
        locale: 'en',
        labels: { toggle: 'Toggle', clear: 'Clear' },
        required: false,
        errormessage: 'Required field',
    };

    afterEach(() => {
        if (wrapper) {
            wrapper.unmount();
        }
        wrapper = null;
        document.body.innerHTML = '';
        vi.unstubAllGlobals();
        vi.restoreAllMocks();
    });

    it('renders a flatpickr bound to the value with name, form and errormessage', async () => {
        wrapper = mount(DateField, { props: defaultProps });
        await wrapper.vm.$nextTick();

        const hiddenInput = wrapper.find('input[type="hidden"]');
        expect(hiddenInput.attributes('value')).toBe('2026-07-06 10:00:00');
        expect(hiddenInput.attributes('name')).toBe('fields[publishdate]');
        expect(hiddenInput.attributes('form')).toBe('editcontent');
        expect(hiddenInput.attributes('data-errormessage')).toBe('Required field');

        // altInput is the visible companion input created by flatpickr
        expect(wrapper.find('.editor--date.input').exists()).toBe(true);
    });

    it('updates the bound value when a date is picked', async () => {
        wrapper = mount(DateField, { props: defaultProps });
        await wrapper.vm.$nextTick();

        wrapper.findComponent(flatPickr).vm.fp?.setDate('2027-01-02 08:30:00', true);
        await wrapper.vm.$nextTick();

        expect(wrapper.find('input[type="hidden"]').attributes('value')).toBe('2027-01-02 08:30:00');
    });

    it('renders toggle and clear buttons with screenreader labels', () => {
        wrapper = mount(DateField, { props: defaultProps });

        const buttons = wrapper.findAll('button');
        expect(buttons).toHaveLength(2);
        expect(buttons[0].attributes('data-toggle')).toBeDefined();
        expect(buttons[0].text()).toBe('Toggle');
        expect(buttons[0].attributes('disabled')).toBeUndefined();
        expect(buttons[1].attributes('data-clear')).toBeDefined();
        expect(buttons[1].text()).toBe('Clear');
        expect(buttons[1].attributes('disabled')).toBeUndefined();
    });

    it('renders without labels and errormessage', () => {
        wrapper = mount(DateField, {
            props: { ...defaultProps, labels: undefined, errormessage: false },
        });

        const buttons = wrapper.findAll('button');
        expect(buttons[0].text()).toBe('');
        expect(buttons[1].text()).toBe('');
        expect(wrapper.find('input[type="hidden"]').attributes('data-errormessage')).toBeUndefined();
    });

    it('disables the buttons and the picker when readonly', () => {
        wrapper = mount(DateField, {
            props: { ...defaultProps, readonly: true },
        });

        const buttons = wrapper.findAll('button');
        expect(buttons[0].attributes('disabled')).toBeDefined();
        expect(buttons[1].attributes('disabled')).toBeDefined();
        expect(buttons[0].classes()).toContain('btn-outline-secondary');
        expect(buttons[1].classes()).toContain('btn-outline-secondary');
    });

    it('does not enable the time picker in date mode', () => {
        wrapper = mount(DateField, { props: defaultProps });
        expect(document.querySelector('.flatpickr-time')).toBeNull();
    });

    it('enables the time picker in datetime mode', () => {
        wrapper = mount(DateField, {
            props: { ...defaultProps, mode: 'datetime' },
        });
        expect(document.querySelector('.flatpickr-time')).not.toBeNull();
    });

    it('loads the flatpickr locale for non-english locales', () => {
        wrapper = mount(DateField, {
            props: { ...defaultProps, locale: 'nl' },
        });

        const weekdays = [...document.querySelectorAll('.flatpickr-weekday')].map(el => el.textContent.trim());
        expect(weekdays).toContain('ma'); // Dutch for Monday
    });

    it('uses the default english locale otherwise', () => {
        wrapper = mount(DateField, { props: defaultProps });

        const weekdays = [...document.querySelectorAll('.flatpickr-weekday')].map(el => el.textContent.trim());
        expect(weekdays).toContain('Mon');
    });

    it('marks the visible input required on mount when required and empty', async () => {
        wrapper = mount(DateField, {
            props: { ...defaultProps, required: true, value: '' },
        });
        await wrapper.vm.$nextTick();

        expect(wrapper.find('.editor--date.input').attributes('required')).toBeDefined();
    });

    it('does not surface the native validation popup on mount for an empty required field', async () => {
        const reportValiditySpy = vi.spyOn(HTMLInputElement.prototype, 'reportValidity').mockReturnValue(true);

        wrapper = mount(DateField, {
            props: { ...defaultProps, required: true, value: '' },
        });
        await wrapper.vm.$nextTick();

        // The required attribute is still applied so the browser blocks submission…
        expect(wrapper.find('.editor--date.input').attributes('required')).toBeDefined();
        // …but the intrusive validation bubble must not be triggered on initial load.
        expect(reportValiditySpy).not.toHaveBeenCalled();
    });

    it('surfaces the validation state after an update, not on mount', async () => {
        const reportValiditySpy = vi.spyOn(HTMLInputElement.prototype, 'reportValidity').mockReturnValue(true);

        wrapper = mount(DateField, {
            props: { ...defaultProps, required: true, value: '' },
        });
        await wrapper.vm.$nextTick();
        expect(reportValiditySpy).not.toHaveBeenCalled();

        // A subsequent update (e.g. re-render after user interaction) may report validity.
        await wrapper.setProps({ readonly: true });

        expect(reportValiditySpy).toHaveBeenCalled();
    });

    it('removes required from the visible input on update when a date is set', async () => {
        wrapper = mount(DateField, {
            props: { ...defaultProps, required: true },
        });

        const altInput = wrapper.find('.editor--date.input');
        altInput.element.setAttribute('required', 'required');

        await wrapper.setProps({ readonly: true });

        expect(altInput.attributes('required')).toBeUndefined();
    });

    it('skips the required fix when the picker input is missing', async () => {
        wrapper = mount(DateField, {
            props: { ...defaultProps, required: true, value: '' },
            global: { stubs: { 'flat-pickr': true } },
        });

        // The stub renders no .editor--date.input; the update must not throw
        await wrapper.setProps({ readonly: true });

        expect(wrapper.find('.editor--date.input').exists()).toBe(false);
    });

    it('leaves the visible input alone on update when not required', async () => {
        wrapper = mount(DateField, { props: { ...defaultProps, value: '' } });

        await wrapper.setProps({ readonly: true });

        expect(wrapper.find('.editor--date.input').attributes('required')).toBeUndefined();
    });
});
