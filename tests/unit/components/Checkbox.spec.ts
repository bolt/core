import { mount } from '@vue/test-utils';
import Checkbox from '@/editor/Components/Checkbox.vue';
import { describe, it, expect } from 'vitest';

describe('EditorCheckbox Component', () => {
    const defaultProps = {
        value: true,
        name: 'fields[published]',
        id: 'field-published',
        required: false,
        readonly: false,
        label: 'Published',
        mode: 'switch',
    };

    it('renders a checked checkbox with its label and hidden submit value', () => {
        const wrapper = mount(Checkbox, { props: defaultProps });

        const checkbox = wrapper.find('input[type="checkbox"]');
        expect((checkbox.element as HTMLInputElement).checked).toBe(true);
        expect(checkbox.attributes('id')).toBe('field-published');
        expect(checkbox.attributes('name')).toBe('fields[published]');
        expect(wrapper.find('label').text()).toBe('Published');
        expect(wrapper.find('.custom-control').classes()).toContain('form-switch');
        expect((wrapper.find('input[type="hidden"]').element as HTMLInputElement).value).toBe('true');
    });

    it('renders unchecked without the switch class in checkbox mode', () => {
        const wrapper = mount(Checkbox, {
            props: { ...defaultProps, value: false, mode: 'checkbox' },
        });

        expect((wrapper.find('input[type="checkbox"]').element as HTMLInputElement).checked).toBe(false);
        expect(wrapper.find('.custom-control').classes()).not.toContain('form-switch');
        expect((wrapper.find('input[type="hidden"]').element as HTMLInputElement).value).toBe('false');
    });

    it('mirrors checkbox changes into the hidden submit value', async () => {
        const wrapper = mount(Checkbox, {
            props: { ...defaultProps, value: false },
        });

        await wrapper.find('input[type="checkbox"]').setValue(true);

        expect((wrapper.find('input[type="hidden"]').element as HTMLInputElement).value).toBe('true');
    });
});
