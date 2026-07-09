import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import Email from '@/editor/Components/Email.vue';
import { describe, it, expect, afterEach } from 'vitest';

describe('EditorEmail Component', () => {
    let wrapper: VueWrapper<ComponentPublicInstance> | null = null;

    const defaultProps = {
        value: 'info@example.org',
        name: 'fields[email]',
        id: 'field-email',
        required: true,
        readonly: false,
        errormessage: 'Error!',
        pattern: '.+@.+',
        placeholder: 'Email…',
    };

    afterEach(() => {
        if (wrapper) {
            wrapper.unmount();
        }
        wrapper = null;
    });

    it('renders an email input with the value and attributes', () => {
        wrapper = mount(Email, { props: defaultProps });

        const input = wrapper.find('input');
        expect(input.attributes('type')).toBe('email');
        expect(input.element.value).toBe('info@example.org');
        expect(input.attributes('id')).toBe('field-email');
        expect(input.attributes('name')).toBe('fields[email]');
        expect(input.attributes('required')).toBeDefined();
        expect(input.attributes('readonly')).toBeUndefined();
        expect(input.attributes('data-errormessage')).toBe('Error!');
        expect(input.attributes('pattern')).toBe('.+@.+');
        expect(input.attributes('placeholder')).toBe('Email…');
    });

    it('renders an envelope icon', () => {
        wrapper = mount(Email, { props: defaultProps });
        expect(wrapper.find('i.fa-envelope').exists()).toBe(true);
    });

    it('omits optional attributes when they are false', () => {
        wrapper = mount(Email, {
            props: {
                ...defaultProps,
                errormessage: false,
                pattern: false,
                placeholder: false,
                required: false,
            },
        });

        const input = wrapper.find('input');
        expect(input.attributes('data-errormessage')).toBeUndefined();
        expect(input.attributes('pattern')).toBeUndefined();
        expect(input.attributes('placeholder')).toBeUndefined();
        expect(input.attributes('required')).toBeUndefined();
    });

    it('renders readonly when set', () => {
        wrapper = mount(Email, { props: { ...defaultProps, readonly: true } });
        expect(wrapper.find('input').attributes('readonly')).toBeDefined();
    });
});
