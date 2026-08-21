import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Textarea from '@/editor/Components/Textarea.vue';

const defaultProps = { id: 'field-body', value: 'Body copy', name: 'fields[body]' };

describe('EditorTextarea', () => {
    it('renders the unescaped value and field metadata', async () => {
        const wrapper = mount(Textarea, {
            propsData: {
                ...defaultProps,
                value: 'Fish &amp; Chips',
                required: true,
                errormessage: 'Required',
                placeholder: 'Write something',
                maxlength: '200',
            },
        });

        // The `value` mixin fills `rawVal` in mounted(), so the textarea is populated a tick later.
        await nextTick();

        const textarea = wrapper.find('textarea');
        // The `value` mixin round-trips the prop through a textarea node, so
        // entities arriving from Twig are decoded before they are displayed.
        expect((textarea.element as HTMLTextAreaElement).value).toBe('Fish & Chips');
        expect(textarea.attributes('id')).toBe('field-body');
        expect(textarea.attributes('name')).toBe('fields[body]');
        expect(textarea.attributes('title')).toBe('fields[body]');
        expect(textarea.attributes('required')).toBeDefined();
        expect(textarea.attributes('data-errormessage')).toBe('Required');
        expect(textarea.attributes('placeholder')).toBe('Write something');
        expect(textarea.attributes('maxlength')).toBe('200');
    });

    it('omits optional attributes when unset', () => {
        const wrapper = mount(Textarea, { propsData: defaultProps });

        const textarea = wrapper.find('textarea');
        expect(textarea.attributes('required')).toBeUndefined();
        expect(textarea.attributes('readonly')).toBeUndefined();
        expect(textarea.attributes('placeholder')).toBeUndefined();
        expect(textarea.attributes('maxlength')).toBeUndefined();
    });

    it('marks the textarea readonly', () => {
        const wrapper = mount(Textarea, { propsData: { ...defaultProps, readonly: true } });

        expect(wrapper.find('textarea').attributes('readonly')).toBeDefined();
    });

    it('treats a numeric height as a rows count', () => {
        const wrapper = mount(Textarea, { propsData: { ...defaultProps, height: 12 } });

        const textarea = wrapper.find('textarea');
        expect(textarea.attributes('rows')).toBe('12');
        expect((textarea.element as HTMLTextAreaElement).style.height).toBe('');
    });

    it('treats a height with a unit as a css height', () => {
        const wrapper = mount(Textarea, { propsData: { ...defaultProps, height: '400px' } });

        const textarea = wrapper.find('textarea');
        expect((textarea.element as HTMLTextAreaElement).style.height).toBe('400px');
        expect(textarea.attributes('rows')).toBeUndefined();
    });

    it('keeps the rendered value in step with typing', async () => {
        const wrapper = mount(Textarea, { propsData: defaultProps });

        await wrapper.find('textarea').setValue('Edited');

        expect((wrapper.find('textarea').element as HTMLTextAreaElement).value).toBe('Edited');
    });
});
