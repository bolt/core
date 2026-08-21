import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import NumberField from '@/editor/Components/Number.vue';

describe('EditorNumber', () => {
    it('renders a number input carrying the value and field metadata', async () => {
        const wrapper = mount(NumberField, {
            propsData: {
                id: 'field-weight',
                value: '42',
                name: 'fields[weight]',
                step: 1,
                min: 0,
                max: 100,
                required: true,
                readonly: false,
                errormessage: 'Out of range',
                placeholder: 'Weight',
            },
        });

        // The `value` mixin fills `val` in mounted(), so the input is populated a tick later.
        await nextTick();

        const input = wrapper.find('input');
        expect(input.attributes('type')).toBe('number');
        expect((input.element as HTMLInputElement).value).toBe('42');
        expect(input.attributes('id')).toBe('field-weight');
        expect(input.attributes('name')).toBe('fields[weight]');
        expect(input.attributes('step')).toBe('1');
        expect(input.attributes('min')).toBe('0');
        expect(input.attributes('max')).toBe('100');
        expect(input.attributes('required')).toBeDefined();
        expect(input.attributes('data-errormessage')).toBe('Out of range');
        expect(input.attributes('placeholder')).toBe('Weight');
    });

    it('omits optional attributes when unset', () => {
        const wrapper = mount(NumberField, { propsData: { value: '1', name: 'fields[weight]' } });

        const input = wrapper.find('input');
        expect(input.attributes('required')).toBeUndefined();
        expect(input.attributes('readonly')).toBeUndefined();
        expect(input.attributes('min')).toBeUndefined();
        expect(input.attributes('max')).toBeUndefined();
    });

    it('marks the input readonly', () => {
        const wrapper = mount(NumberField, {
            propsData: { value: '1', name: 'fields[weight]', readonly: true },
        });

        expect(wrapper.find('input').attributes('readonly')).toBeDefined();
    });

    it('keeps the rendered value in step with typing', async () => {
        const wrapper = mount(NumberField, { propsData: { value: '1', name: 'fields[weight]' } });

        await wrapper.find('input').setValue('99');

        expect((wrapper.find('input').element as HTMLInputElement).value).toBe('99');
    });
});
