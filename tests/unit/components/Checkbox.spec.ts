import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import Checkbox from '@/editor/Components/Checkbox.vue';

const defaultProps = {
    value: false,
    name: 'fields[featured]',
    id: 'field-featured',
    required: false,
    readonly: false,
    label: 'Featured',
    mode: 'check',
};

const visible = (wrapper: ReturnType<typeof mount>) => wrapper.find('input[type="checkbox"]');
// The checkbox itself is never submitted; this hidden twin carries the value so
// that an unchecked box still posts "false".
const submitted = (wrapper: ReturnType<typeof mount>) =>
    wrapper.find('input[type="hidden"]').element as HTMLInputElement;

describe('EditorCheckbox', () => {
    it('renders the label and field metadata', () => {
        const wrapper = mount(Checkbox, { propsData: defaultProps });

        expect(wrapper.find('label').text()).toBe('Featured');
        expect(wrapper.find('label').attributes('for')).toBe('fields[featured]');
        expect(visible(wrapper).attributes('id')).toBe('field-featured');
        expect(visible(wrapper).attributes('name')).toBe('fields[featured]');
    });

    it('reflects an unchecked value', () => {
        const wrapper = mount(Checkbox, { propsData: defaultProps });

        expect((visible(wrapper).element as HTMLInputElement).checked).toBe(false);
        expect(submitted(wrapper).value).toBe('false');
    });

    it('reflects a checked value', () => {
        const wrapper = mount(Checkbox, { propsData: { ...defaultProps, value: true } });

        expect((visible(wrapper).element as HTMLInputElement).checked).toBe(true);
        expect(submitted(wrapper).value).toBe('true');
    });

    it('mirrors a change into the submitted value', async () => {
        const wrapper = mount(Checkbox, { propsData: defaultProps });

        await visible(wrapper).setChecked(true);
        expect(submitted(wrapper).value).toBe('true');

        await visible(wrapper).setChecked(false);
        expect(submitted(wrapper).value).toBe('false');
    });

    it('renders switch mode with the switch classes', () => {
        const wrapper = mount(Checkbox, { propsData: { ...defaultProps, mode: 'switch' } });

        const control = wrapper.find('.custom-control');
        expect(control.classes()).toContain('form-switch');
        expect(control.classes()).toContain('form-check');
    });

    it('does not add the switch class in check mode', () => {
        const wrapper = mount(Checkbox, { propsData: defaultProps });

        expect(wrapper.find('.custom-control').classes()).not.toContain('form-switch');
    });
});
