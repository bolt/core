import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import Text from '@/editor/Components/Text.vue';
import { eventBus } from '@/eventBus';
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';

describe('EditorText Component', () => {
    let wrapper: VueWrapper<ComponentPublicInstance> | null = null;

    const defaultProps = {
        value: 'Initial text',
        name: 'fields[title]',
        type: 'text',
        disabled: false,
        id: 'field-title',
        required: true,
        readonly: false,
        errormessage: 'Error!',
        pattern: '[a-z]+',
        placeholder: 'Type here…',
        autofocus: false,
    };

    beforeEach(() => {
        vi.clearAllMocks();
        eventBus.all.clear();
    });

    afterEach(() => {
        if (wrapper) {
            wrapper.unmount();
        }
        wrapper = null;
    });

    it('renders an input with the unescaped value and attributes', async () => {
        wrapper = mount(Text, {
            props: { ...defaultProps, value: 'Fish &amp; Chips' },
        });
        await wrapper.vm.$nextTick();

        const input = wrapper.find('input');
        expect((input.element as HTMLInputElement).value).toBe('Fish & Chips');
        expect(input.attributes('id')).toBe('field-title');
        expect(input.attributes('name')).toBe('fields[title]');
        expect(input.attributes('title')).toBe('fields[title]');
        expect(input.attributes('required')).toBeDefined();
        expect(input.attributes('readonly')).toBeUndefined();
        expect(input.attributes('disabled')).toBeUndefined();
        expect(input.attributes('data-errormessage')).toBe('Error!');
        expect(input.attributes('pattern')).toBe('[a-z]+');
        expect(input.attributes('placeholder')).toBe('Type here…');
    });

    it('accepts numeric values and renders them as text input values', () => {
        wrapper = mount(Text, {
            props: { ...defaultProps, value: 50 },
        });

        expect((wrapper.find('input').element as HTMLInputElement).value).toBe('50');
    });

    it('omits optional attributes when they are false', () => {
        wrapper = mount(Text, {
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

    it('renders disabled, readonly and autofocus when set', () => {
        wrapper = mount(Text, {
            props: {
                ...defaultProps,
                disabled: true,
                readonly: true,
                autofocus: true,
            },
        });

        const input = wrapper.find('input');
        expect(input.attributes('disabled')).toBeDefined();
        expect(input.attributes('readonly')).toBeDefined();
        expect(input.attributes('autofocus')).toBeDefined();
    });

    it('maps type "large" to the form-control-lg class', () => {
        wrapper = mount(Text, { props: { ...defaultProps, type: 'large' } });
        expect(wrapper.find('input').classes()).toContain('form-control-lg');
    });

    it('uses the type prop as class for other types', () => {
        wrapper = mount(Text, { props: { ...defaultProps, type: 'slug' } });
        expect(wrapper.find('input').classes()).toContain('slug');
    });

    it('does not emit slugify-from-title while generation is off', async () => {
        const emitSpy = vi.spyOn(eventBus, 'emit');
        wrapper = mount(Text, { props: defaultProps });

        await wrapper.find('input').setValue('New Title');

        expect(emitSpy.mock.calls.some(([event]) => event === 'slugify-from-title')).toBe(false);
    });

    it('emits slugify-from-title on input when generation is on', async () => {
        const emitSpy = vi.spyOn(eventBus, 'emit');
        wrapper = mount(Text, { props: defaultProps });

        eventBus.emit('generate-from-title', {
            sources: ['title'],
            active: true,
        });
        await wrapper.find('input').setValue('New Title');

        expect(emitSpy).toHaveBeenCalledWith('slugify-from-title', {
            source: 'title',
        });
    });

    it('stops emitting slugify-from-title when generation is switched off again', async () => {
        const emitSpy = vi.spyOn(eventBus, 'emit');
        wrapper = mount(Text, { props: defaultProps });

        eventBus.emit('generate-from-title', {
            sources: ['title'],
            active: true,
        });
        eventBus.emit('generate-from-title', {
            sources: ['title'],
            active: false,
        });
        await wrapper.find('input').setValue('New Title');

        expect(emitSpy.mock.calls.some(([event]) => event === 'slugify-from-title')).toBe(false);
    });

    it('ignores generation events for unrelated source fields', async () => {
        const emitSpy = vi.spyOn(eventBus, 'emit');
        wrapper = mount(Text, { props: defaultProps });

        eventBus.emit('generate-from-title', {
            sources: ['other_title'],
            active: true,
        });
        await wrapper.find('input').setValue('New Title');

        expect(emitSpy.mock.calls.some(([event]) => event === 'slugify-from-title')).toBe(false);
    });

    it('unsubscribes from generate-from-title when destroyed', () => {
        wrapper = mount(Text, { props: defaultProps });

        // Mounting registers exactly one listener
        expect(eventBus.all.get('generate-from-title')).toHaveLength(1);

        wrapper.unmount();

        const listeners = eventBus.all.get('generate-from-title');
        expect(listeners === undefined || listeners.length === 0).toBe(true);
        wrapper = null; // Prevent afterEach from unmounting again
    });
});
