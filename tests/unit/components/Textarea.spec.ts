import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import Textarea from '@/editor/Components/Textarea.vue';
import { describe, it, expect, afterEach } from 'vitest';

describe('EditorTextarea Component', () => {
    let wrapper: VueWrapper<ComponentPublicInstance> | null = null;

    const defaultProps = {
        id: 'field-body',
        value: 'Initial body',
        name: 'fields[body]',
        required: true,
        readonly: false,
        errormessage: 'Error!',
        placeholder: 'Write here…',
        maxlength: '200',
    };

    afterEach(() => {
        if (wrapper) {
            wrapper.unmount();
        }
        wrapper = null;
    });

    it('renders a textarea with the unescaped value and attributes', async () => {
        wrapper = mount(Textarea, {
            props: { ...defaultProps, value: 'Fish &amp; Chips' },
        });
        await wrapper.vm.$nextTick();

        const textarea = wrapper.find('textarea');
        expect((textarea.element as HTMLTextAreaElement).value).toBe('Fish & Chips');
        expect(textarea.attributes('id')).toBe('field-body');
        expect(textarea.attributes('name')).toBe('fields[body]');
        expect(textarea.attributes('title')).toBe('fields[body]');
        expect(textarea.attributes('required')).toBeDefined();
        expect(textarea.attributes('readonly')).toBeUndefined();
        expect(textarea.attributes('data-errormessage')).toBe('Error!');
        expect(textarea.attributes('placeholder')).toBe('Write here…');
        expect(textarea.attributes('maxlength')).toBe('200');
    });

    it('omits optional attributes when they are unset or false', () => {
        wrapper = mount(Textarea, {
            props: {
                ...defaultProps,
                errormessage: false,
                placeholder: false,
                maxlength: undefined,
                required: false,
            },
        });

        const textarea = wrapper.find('textarea');
        expect(textarea.attributes('data-errormessage')).toBeUndefined();
        expect(textarea.attributes('placeholder')).toBeUndefined();
        expect(textarea.attributes('maxlength')).toBeUndefined();
        expect(textarea.attributes('required')).toBeUndefined();
    });

    it('renders readonly when set', () => {
        wrapper = mount(Textarea, {
            props: { ...defaultProps, readonly: true },
        });
        expect(wrapper.find('textarea').attributes('readonly')).toBeDefined();
    });

    it('sets the rows attribute for a numeric height', () => {
        wrapper = mount(Textarea, { props: { ...defaultProps, height: '5' } });

        const textarea = wrapper.find('textarea');
        expect(textarea.attributes('rows')).toBe('5');
        expect(textarea.element.style.height).toBe('');
    });

    it('sets the css height for a non-numeric height', () => {
        wrapper = mount(Textarea, {
            props: { ...defaultProps, height: '150px' },
        });

        const textarea = wrapper.find('textarea');
        expect(textarea.element.style.height).toBe('150px');
        expect(textarea.attributes('rows')).toBeUndefined();
    });

    it('sets neither rows nor css height when height is unset', () => {
        wrapper = mount(Textarea, { props: defaultProps });

        const textarea = wrapper.find('textarea');
        expect(textarea.element.style.height).toBe('');
        expect(textarea.attributes('rows')).toBeUndefined();
    });

    it('updates the bound value when the textarea changes', async () => {
        wrapper = mount(Textarea, { props: defaultProps });

        const textarea = wrapper.find('textarea');
        await textarea.setValue('Changed body');

        expect((textarea.element as HTMLTextAreaElement).value).toBe('Changed body');
    });
});
