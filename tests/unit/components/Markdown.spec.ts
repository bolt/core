import { mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import Markdown from '@/editor/Components/Markdown.vue';

type EditorOptions = {
    element: HTMLTextAreaElement;
    initialValue?: string;
    spellChecker: boolean;
    status: boolean;
    forceSync: boolean;
    toolbar: string[];
};

// Markdown.vue constructs EasyMDE itself rather than embedding a component, so
// there is no tag to stub (see helpers/stubs.ts). This stands in for the editor:
// it records how it was built and lets a spec type into it.
const { editors } = vi.hoisted(() => ({
    editors: [] as {
        options: EditorOptions;
        content: string;
        type: (markdown: string) => void;
        toTextArea: ReturnType<typeof vi.fn>;
    }[],
}));

// A plain function, not an arrow: Markdown.vue calls `new EasyMDE(...)`.
vi.mock('easymde', () => ({
    default: vi.fn(function (options: EditorOptions) {
        const listeners: (() => void)[] = [];
        const editor = {
            options,
            content: options.initialValue ?? '',
            type(markdown: string) {
                editor.content = markdown;
                listeners.forEach(listener => listener());
            },
            toTextArea: vi.fn(),
        };
        editors.push(editor);

        return {
            codemirror: { on: (_event: string, listener: () => void) => listeners.push(listener) },
            value: () => editor.content,
            toTextArea: editor.toTextArea,
        };
    }),
}));

const defaultProps = { modelValue: '# Heading', name: 'fields[body]' };

const mountMarkdown = (props: Record<string, unknown> = {}) =>
    mount(Markdown, { props: { ...defaultProps, ...props } });

const editor = () => editors[editors.length - 1];

beforeEach(() => {
    editors.length = 0;
});

describe('EditorMarkdown', () => {
    it('identifies the editor by the field name', () => {
        const wrapper = mountMarkdown();

        const textarea = wrapper.find('textarea');
        expect(textarea.attributes('id')).toBe('fields[body]');
        expect(textarea.attributes('name')).toBe('fields[body]');
        expect(editor().options.element).toBe(textarea.element);
    });

    it('strips the surrounding quotes Twig adds before loading the value', () => {
        mountMarkdown({ modelValue: '"# Heading"' });

        expect(editor().options.initialValue).toBe('# Heading');
    });

    it('loads an unquoted value unchanged', () => {
        mountMarkdown();

        expect(editor().options.initialValue).toBe('# Heading');
    });

    it('starts empty when there is no value', () => {
        mountMarkdown({ modelValue: undefined });

        // `strip` returns undefined for a falsy value; EasyMDE starts empty.
        expect(editor().options.initialValue).toBeUndefined();
    });

    it('takes edits back from the editor', () => {
        const wrapper = mountMarkdown();

        editor().type('# Edited');

        expect(wrapper.emitted('update:modelValue')).toEqual([['# Edited']]);
    });

    it('keeps the textarea in sync for the form, without spell checker or status bar', () => {
        mountMarkdown();

        expect(editor().options.forceSync).toBe(true);
        expect(editor().options.spellChecker).toBe(false);
        expect(editor().options.status).toBe(false);
        expect(editor().options.toolbar).toContain('fullscreen');
    });

    it('hands the textarea back when unmounted', () => {
        const wrapper = mountMarkdown();

        wrapper.unmount();

        expect(editor().toTextArea).toHaveBeenCalledTimes(1);
    });
});
