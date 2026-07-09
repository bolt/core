import { mount } from '@vue/test-utils';
import Html from '@/editor/Components/Html.vue';
import { describe, it, expect, vi } from 'vitest';

// Stand-in for the trumbowyg wrapper: a plain textarea with v-model semantics
vi.mock('vue-trumbowyg', () => ({
    default: {
        name: 'trumbowyg',
        props: ['modelValue', 'config', 'name', 'id'],
        emits: ['update:modelValue'],
        template:
            '<textarea :id="id" :name="name" :value="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)"></textarea>',
    },
}));
vi.mock('trumbowyg/dist/ui/trumbowyg.css', () => ({}));

describe('EditorHtml Component', () => {
    it('renders the editor with the stripped value', async () => {
        const wrapper = mount(Html, {
            props: {
                value: '"<p>Hello</p>"',
                name: 'fields[body]',
                id: 'field-body',
            },
        });
        await wrapper.vm.$nextTick();

        const textarea = wrapper.get('textarea');
        expect((textarea.element as HTMLTextAreaElement).value).toBe('<p>Hello</p>');
        expect(textarea.attributes('name')).toBe('fields[body]');
        expect(textarea.attributes('id')).toBe('field-body');
    });

    it('passes the toolbar configuration to the editor', () => {
        const wrapper = mount(Html, {
            props: { value: '', name: 'fields[body]' },
        });

        const editor = wrapper.findComponent({ name: 'trumbowyg' });
        expect(editor.props('config').btns).toContainEqual(['viewHTML']);
        expect(editor.props('config').btns).toContainEqual(['formatting']);
    });

    it('keeps edits in the editor value', async () => {
        const wrapper = mount(Html, {
            props: { value: '"<p>Hello</p>"', name: 'fields[body]' },
        });
        await wrapper.vm.$nextTick();

        await wrapper.get('textarea').setValue('<p>Changed</p>');
        expect((wrapper.get('textarea').element as HTMLTextAreaElement).value).toBe('<p>Changed</p>');
    });
});
