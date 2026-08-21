import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Text from '@/editor/Components/Text.vue';
import { emitOnBus, recordBus } from '../helpers/bus';

const defaultProps = {
    value: 'Initial text',
    name: 'fields[title]',
    type: 'text',
    disabled: false,
    id: 'field-title',
    required: true,
    readonly: false,
    errormessage: 'Required',
    pattern: '[a-z]+',
    placeholder: 'Type here',
    autofocus: false,
};

// Slug and Text talk to each other over the root instance's event bus, which a
// Slug field elsewhere on the page would normally drive.
const startGenerating = async (wrapper: ReturnType<typeof mount>) => {
    emitOnBus(wrapper, 'generate-from-title', true);
    await nextTick();
};

describe('EditorText', () => {
    it('renders the unescaped value and field metadata', async () => {
        const wrapper = mount(Text, { propsData: { ...defaultProps, value: 'Fish &amp; Chips' } });
        await nextTick();

        const input = wrapper.find('input');
        expect((input.element as HTMLInputElement).value).toBe('Fish & Chips');
        expect(input.attributes('id')).toBe('field-title');
        expect(input.attributes('name')).toBe('fields[title]');
        expect(input.attributes('title')).toBe('fields[title]');
        expect(input.attributes('type')).toBe('text');
        expect(input.attributes('required')).toBeDefined();
        expect(input.attributes('data-errormessage')).toBe('Required');
        expect(input.attributes('pattern')).toBe('[a-z]+');
        expect(input.attributes('placeholder')).toBe('Type here');
    });

    it('omits optional attributes when unset or false', () => {
        const wrapper = mount(Text, { propsData: { value: '', name: 'fields[title]' } });

        const input = wrapper.find('input');
        expect(input.attributes('required')).toBeUndefined();
        expect(input.attributes('readonly')).toBeUndefined();
        expect(input.attributes('disabled')).toBeUndefined();
        expect(input.attributes('autofocus')).toBeUndefined();
    });

    it('renders disabled, readonly and autofocus when set', () => {
        const wrapper = mount(Text, {
            propsData: { ...defaultProps, disabled: true, readonly: true, autofocus: true },
        });

        const input = wrapper.find('input');
        expect(input.attributes('disabled')).toBeDefined();
        expect(input.attributes('readonly')).toBeDefined();
        expect(input.attributes('autofocus')).toBeDefined();
    });

    it('maps the "large" type to the bootstrap large class', () => {
        const wrapper = mount(Text, { propsData: { ...defaultProps, type: 'large' } });

        expect(wrapper.find('input').classes()).toContain('form-control-lg');
    });

    it('passes any other type through as a class', () => {
        const wrapper = mount(Text, { propsData: { ...defaultProps, type: 'form-control-sm' } });

        expect(wrapper.find('input').classes()).toContain('form-control-sm');
    });

    it('keeps the rendered value in step with typing', async () => {
        const wrapper = mount(Text, { propsData: defaultProps });
        await nextTick();

        await wrapper.find('input').setValue('New title');

        expect((wrapper.find('input').element as HTMLInputElement).value).toBe('New title');
    });

    it('stays quiet on the bus until slug generation is switched on', async () => {
        const wrapper = mount(Text, { propsData: defaultProps });
        const asked = recordBus(wrapper, 'slugify-from-title');
        await nextTick();

        await wrapper.find('input').setValue('New title');

        expect(asked).toEqual([]);
    });

    it('asks for a new slug on every keystroke once generation is on', async () => {
        const wrapper = mount(Text, { propsData: defaultProps });
        const asked = recordBus(wrapper, 'slugify-from-title');
        await nextTick();
        await startGenerating(wrapper);

        await wrapper.find('input').setValue('New title');
        await wrapper.find('input').setValue('New title 2');

        expect(asked).toHaveLength(2);
    });

    it('stops asking when generation is switched off again', async () => {
        const wrapper = mount(Text, { propsData: defaultProps });
        const asked = recordBus(wrapper, 'slugify-from-title');
        await nextTick();
        await startGenerating(wrapper);

        await wrapper.find('input').setValue('New title');
        emitOnBus(wrapper, 'generate-from-title', false);
        await nextTick();
        await wrapper.find('input').setValue('New title 2');

        expect(asked).toHaveLength(1);
    });

    it.skip('accepts a numeric value and renders it as text', () => {
        // Only passes after 7450e402 ("Keep media crop values numeric") widens
        // the `value` prop to accept a Number. Today a numeric value trips
        // Vue's prop-type check.
        const wrapper = mount(Text, { propsData: { ...defaultProps, value: 50 } });

        expect((wrapper.find('input').element as HTMLInputElement).value).toBe('50');
    });

    it.skip('only regenerates the slug it is scoped to', () => {
        // Only passes on refactor/vue3-composition-api, where the mitt event bus
        // carries `{ sources, active }` / `{ source }` payloads. The Vue 2.7
        // $root bus broadcasts a bare boolean, so every Text field on the page
        // drives every Slug field.
    });
});
