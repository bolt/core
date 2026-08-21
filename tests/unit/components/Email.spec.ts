import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import Email from '@/editor/Components/Email.vue';

describe('EditorEmail', () => {
    it('renders an email input carrying the value and field metadata', () => {
        const wrapper = mount(Email, {
            propsData: {
                value: 'bob@example.org',
                name: 'fields[email]',
                id: 'field-email',
                required: true,
                readonly: false,
                errormessage: 'Not an email',
                pattern: '.+@.+',
                placeholder: 'you@example.org',
            },
        });

        const input = wrapper.find('input');
        expect(input.attributes('type')).toBe('email');
        expect((input.element as HTMLInputElement).value).toBe('bob@example.org');
        expect(input.attributes('id')).toBe('field-email');
        expect(input.attributes('name')).toBe('fields[email]');
        expect(input.attributes('required')).toBeDefined();
        expect(input.attributes('data-errormessage')).toBe('Not an email');
        expect(input.attributes('pattern')).toBe('.+@.+');
        expect(input.attributes('placeholder')).toBe('you@example.org');
    });

    it('renders the envelope icon', () => {
        const wrapper = mount(Email, { propsData: { value: '', name: 'fields[email]' } });

        expect(wrapper.find('i.fa-envelope').exists()).toBe(true);
    });

    it('omits optional attributes when unset', () => {
        const wrapper = mount(Email, { propsData: { value: '', name: 'fields[email]' } });

        const input = wrapper.find('input');
        expect(input.attributes('required')).toBeUndefined();
        expect(input.attributes('readonly')).toBeUndefined();
        expect(input.attributes('pattern')).toBeUndefined();
        expect(input.attributes('placeholder')).toBeUndefined();
    });

    it('marks the input readonly', () => {
        const wrapper = mount(Email, {
            propsData: { value: '', name: 'fields[email]', readonly: true },
        });

        expect(wrapper.find('input').attributes('readonly')).toBeDefined();
    });
});
