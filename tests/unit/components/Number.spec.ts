import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import NumberField from '@/editor/Components/Number.vue';
import { describe, it, expect, afterEach } from 'vitest';

describe('EditorNumber Component', () => {
    let wrapper: VueWrapper<ComponentPublicInstance> | null = null;

    const defaultProps = {
        id: 'field-amount',
        value: '42',
        name: 'fields[amount]',
        step: 1,
        min: 0,
        max: 100,
        required: true,
        readonly: false,
        errormessage: 'Error!',
        pattern: '[0-9]+',
        placeholder: 'Amount…',
    };

    afterEach(() => {
        if (wrapper) {
            wrapper.unmount();
        }
        wrapper = null;
    });

    it('renders a number input with the initial value and attributes', async () => {
        wrapper = mount(NumberField, { props: defaultProps });
        await wrapper.vm.$nextTick();

        const input = wrapper.find('input');
        expect(input.attributes('type')).toBe('number');
        expect((input.element as HTMLInputElement).value).toBe('42');
        expect(input.attributes('id')).toBe('field-amount');
        expect(input.attributes('name')).toBe('fields[amount]');
        expect(input.attributes('step')).toBe('1');
        expect(input.attributes('min')).toBe('0');
        expect(input.attributes('max')).toBe('100');
        expect(input.attributes('required')).toBeDefined();
        expect(input.attributes('readonly')).toBeUndefined();
        expect(input.attributes('data-errormessage')).toBe('Error!');
        expect(input.attributes('pattern')).toBe('[0-9]+');
        expect(input.attributes('placeholder')).toBe('Amount…');
    });

    it('omits optional attributes when they are false', () => {
        wrapper = mount(NumberField, {
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
        wrapper = mount(NumberField, {
            props: { ...defaultProps, readonly: true },
        });
        expect(wrapper.find('input').attributes('readonly')).toBeDefined();
    });

    it('updates the bound value when the input changes', async () => {
        wrapper = mount(NumberField, { props: defaultProps });

        const input = wrapper.find('input');
        await input.setValue('7');

        expect((input.element as HTMLInputElement).value).toBe('7');
    });
});
