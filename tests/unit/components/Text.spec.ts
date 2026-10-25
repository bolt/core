import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Text from '@/editor/Components/Text.vue';
import { eventBus } from '@/eventBus';
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

// Slug and Text talk to each other over the shared event bus, which a Slug
// field elsewhere on the page would normally drive. A Slug names the title
// fields it is generated from; `title` is this field's key.
const setGenerating = async (active: boolean, sources = ['title']) => {
    emitOnBus('generate-from-title', { sources, active });
    await nextTick();
};

describe('EditorText', () => {
    it('renders the unescaped value and field metadata', async () => {
        const wrapper = mount(Text, { props: { ...defaultProps, value: 'Fish &amp; Chips' } });
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
        const wrapper = mount(Text, { props: { value: '', name: 'fields[title]' } });

        const input = wrapper.find('input');
        expect(input.attributes('required')).toBeUndefined();
        expect(input.attributes('readonly')).toBeUndefined();
        expect(input.attributes('disabled')).toBeUndefined();
        expect(input.attributes('autofocus')).toBeUndefined();
    });

    it('renders disabled, readonly and autofocus when set', () => {
        const wrapper = mount(Text, {
            props: { ...defaultProps, disabled: true, readonly: true, autofocus: true },
        });

        const input = wrapper.find('input');
        expect(input.attributes('disabled')).toBeDefined();
        expect(input.attributes('readonly')).toBeDefined();
        expect(input.attributes('autofocus')).toBeDefined();
    });

    it('maps the "large" type to the bootstrap large class', () => {
        const wrapper = mount(Text, { props: { ...defaultProps, type: 'large' } });

        expect(wrapper.find('input').classes()).toContain('form-control-lg');
    });

    it('passes any other type through as a class', () => {
        const wrapper = mount(Text, { props: { ...defaultProps, type: 'form-control-sm' } });

        expect(wrapper.find('input').classes()).toContain('form-control-sm');
    });

    it('keeps the rendered value in step with typing', async () => {
        const wrapper = mount(Text, { props: defaultProps });
        await nextTick();

        await wrapper.find('input').setValue('New title');
        // Re-render: a field that only displayed its prop would be reset to it.
        await wrapper.setProps({ placeholder: 'Changed' });

        expect((wrapper.find('input').element as HTMLInputElement).value).toBe('New title');
    });

    it('stays quiet on the bus until slug generation is switched on', async () => {
        const wrapper = mount(Text, { props: defaultProps });
        const asked = recordBus('slugify-from-title');
        await nextTick();

        await wrapper.find('input').setValue('New title');

        expect(asked).toEqual([]);
    });

    it('asks for a new slug on every keystroke once generation is on', async () => {
        const wrapper = mount(Text, { props: defaultProps });
        const asked = recordBus('slugify-from-title');
        await nextTick();
        await setGenerating(true);

        await wrapper.find('input').setValue('New title');
        await wrapper.find('input').setValue('New title 2');

        expect(asked).toEqual([{ source: 'title' }, { source: 'title' }]);
    });

    it('stops asking when generation is switched off again', async () => {
        const wrapper = mount(Text, { props: defaultProps });
        const asked = recordBus('slugify-from-title');
        await nextTick();
        await setGenerating(true);

        await wrapper.find('input').setValue('New title');
        await setGenerating(false);
        await wrapper.find('input').setValue('New title 2');

        expect(asked).toHaveLength(1);
    });

    it('accepts a numeric value and renders it as text', () => {
        // The media edit page passes the crop values as raw numbers.
        const wrapper = mount(Text, { props: { ...defaultProps, value: 50 } });

        expect((wrapper.find('input').element as HTMLInputElement).value).toBe('50');
    });

    it('ignores generation switched on for other title fields', async () => {
        const wrapper = mount(Text, { props: defaultProps });
        const asked = recordBus('slugify-from-title');
        await setGenerating(true, ['other_title']);

        await wrapper.find('input').setValue('New title');

        expect(asked).toEqual([]);
    });

    it('stops listening to the bus once unmounted', () => {
        const wrapper = mount(Text, { props: defaultProps });
        expect(eventBus.all.get('generate-from-title')).toHaveLength(1);

        wrapper.unmount();

        expect(eventBus.all.get('generate-from-title') ?? []).toHaveLength(0);
    });
});
