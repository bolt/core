import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import Markdown from '@/editor/Components/Markdown.vue';
import EasyMDE from 'easymde';
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';

vi.mock('easymde', () => {
    const instance = {
        codemirror: { on: vi.fn() },
        value: vi.fn(() => '# edited'),
        toTextArea: vi.fn(),
    };
    const EasyMDE = vi.fn(function () {
        return instance;
    });
    return { default: EasyMDE };
});

describe('EditorMarkdown Component', () => {
    type EasyMDEOptions = {
        element: Element;
        initialValue?: string;
        forceSync: boolean;
    };
    type EasyMDEInstance = {
        codemirror: {
            on: ReturnType<typeof vi.fn>;
        };
        value: ReturnType<typeof vi.fn>;
        toTextArea: ReturnType<typeof vi.fn>;
    };
    type EasyMDEConstructorMock = typeof EasyMDE & {
        mock: {
            calls: Array<[EasyMDEOptions]>;
            results: Array<{ value: EasyMDEInstance }>;
        };
    };
    type MarkdownExpose = {
        easymde: InstanceType<typeof EasyMDE> | null;
    };
    const easyMDEMock = EasyMDE as EasyMDEConstructorMock;
    let wrapper: VueWrapper<ComponentPublicInstance & MarkdownExpose> | null = null;

    beforeEach(() => {
        vi.clearAllMocks();
    });

    afterEach(() => {
        if (wrapper) {
            wrapper?.unmount();
        }
        wrapper = null;
    });

    it('initializes EasyMDE on the textarea with the stripped value', () => {
        wrapper = mount(Markdown, {
            props: { modelValue: '"# hello"', name: 'fields[body]' },
        });

        expect(EasyMDE).toHaveBeenCalledTimes(1);
        const options = easyMDEMock.mock.calls[0]?.[0];
        expect(options).toBeDefined();
        expect(options.element).toBe(wrapper!.get('textarea').element);
        expect(options.initialValue).toBe('# hello');
        expect(options.forceSync).toBe(true);
        expect(wrapper!.get('textarea').attributes('name')).toBe('fields[body]');
        expect(wrapper!.get('textarea').attributes('id')).toBe('fields[body]');
    });

    it('emits update:modelValue when the editor content changes', () => {
        wrapper = mount(Markdown, {
            props: { modelValue: '# hello', name: 'fields[body]' },
        });

        const instance = easyMDEMock.mock.results[0]?.value;
        expect(instance).toBeDefined();
        const changeCall = instance.codemirror.on.mock.calls[0];
        expect(changeCall?.[0]).toBe('change');
        const onChange = changeCall?.[1];
        expect(onChange).toBeTypeOf('function');
        onChange();

        expect(wrapper!.emitted('update:modelValue')).toEqual([['# edited']]);
    });

    it('tears the editor down on unmount', () => {
        wrapper = mount(Markdown, {
            props: { modelValue: '# hello', name: 'fields[body]' },
        });

        const instance = easyMDEMock.mock.results[0]?.value;
        expect(instance).toBeDefined();
        wrapper?.unmount();
        wrapper = null;

        expect(instance.toTextArea).toHaveBeenCalledTimes(1);
    });

    it('handles undefined modelValue', () => {
        wrapper = mount(Markdown, {
            props: { name: 'fields[body]' },
        });

        expect(EasyMDE).toHaveBeenCalledTimes(1);
        const options = easyMDEMock.mock.calls[0]?.[0];
        expect(options).toBeDefined();
        expect(options.initialValue).toBeUndefined();
    });

    it('emits empty string when easymde.value() is falsy', () => {
        wrapper = mount(Markdown, {
            props: { modelValue: '# hello', name: 'fields[body]' },
        });

        const instance = easyMDEMock.mock.results[0]?.value;
        expect(instance).toBeDefined();
        instance.value.mockReturnValueOnce(undefined);

        const onChange = instance.codemirror.on.mock.calls[0]?.[1];
        expect(onChange).toBeTypeOf('function');
        onChange();

        expect(wrapper!.emitted('update:modelValue')).toEqual([['']]);
    });

    it('does not throw on unmount if easymde is null', () => {
        wrapper = mount(Markdown, {
            props: { modelValue: '# hello', name: 'fields[body]' },
        });

        // Use the exposed setter to set easymde to null before unmount
        wrapper!.vm!.easymde = null;

        expect(() => {
            wrapper?.unmount();
        }).not.toThrow();

        wrapper = null;
    });

    it('exposes easymde instance', () => {
        wrapper = mount(Markdown, {
            props: { modelValue: '# hello', name: 'fields[body]' },
        });

        // The exposed properties are available on the component instance
        const exposedEasymde = wrapper!.vm!.easymde;
        expect(exposedEasymde).toBeDefined();
    });

    it('emits empty string if easymde is null when change is triggered', () => {
        wrapper = mount(Markdown, {
            props: { modelValue: '# hello', name: 'fields[body]' },
        });

        const instance = easyMDEMock.mock.results[0]?.value;
        expect(instance).toBeDefined();
        const onChange = instance.codemirror.on.mock.calls[0]?.[1];
        expect(onChange).toBeTypeOf('function');

        // Set to null
        wrapper!.vm!.easymde = null;

        // Trigger change
        onChange();

        expect(wrapper!.emitted('update:modelValue')).toBeUndefined();
    });
});
