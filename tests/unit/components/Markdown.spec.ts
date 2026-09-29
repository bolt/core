import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Markdown from '@/editor/Components/Markdown.vue';
import { easyMdeStub } from '../helpers/stubs';

const defaultProps = { value: '# Heading', name: 'fields[body]' };

const mountMarkdown = (props: Record<string, unknown> = {}) =>
    mount(Markdown, { propsData: { ...defaultProps, ...props }, stubs: { 'vue-easymde': easyMdeStub } });

const editor = (wrapper: ReturnType<typeof mount>) => wrapper.findComponent(easyMdeStub);

describe('EditorMarkdown', () => {
    it('identifies the editor by the field name', async () => {
        const wrapper = mountMarkdown();
        await nextTick();

        expect(editor(wrapper).props('id')).toBe('fields[body]');
        expect(editor(wrapper).props('name')).toBe('fields[body]');
    });

    it('strips the surrounding quotes Twig adds before loading the value', async () => {
        const wrapper = mountMarkdown({ value: '"# Heading"' });
        await nextTick();

        expect(editor(wrapper).props('value')).toBe('# Heading');
    });

    it('loads an unquoted value unchanged', async () => {
        const wrapper = mountMarkdown();
        await nextTick();

        expect(editor(wrapper).props('value')).toBe('# Heading');
    });

    it('starts empty when there is no value', async () => {
        const wrapper = mountMarkdown({ value: '' });
        await nextTick();

        expect((wrapper.find('textarea').element as HTMLTextAreaElement).value).toBe('');
    });

    it('takes edits back from the editor', async () => {
        const wrapper = mountMarkdown();
        await nextTick();

        (editor(wrapper).vm as unknown as { type: (markdown: string) => void }).type('# Edited');
        await nextTick();

        expect(editor(wrapper).props('value')).toBe('# Edited');
    });

    it('turns off the spell checker and status bar, and allows full screen', async () => {
        const wrapper = mountMarkdown();
        await nextTick();

        expect(editor(wrapper).props('configs')).toEqual({
            spellChecker: false,
            status: false,
            toggleFullScreen: true,
        });
    });
});
