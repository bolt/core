import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Html from '@/editor/Components/Html.vue';
import { trumbowygStub } from '../helpers/stubs';

const defaultProps = {
    value: 'Some <strong>html</strong>',
    label: 'Body',
    name: 'fields[body]',
    id: 'field-body',
};

const mountHtml = (props: Record<string, unknown> = {}) =>
    mount(Html, { propsData: { ...defaultProps, ...props }, stubs: { trumbowyg: trumbowygStub } });

const editor = (wrapper: ReturnType<typeof mount>) => wrapper.findComponent(trumbowygStub);

describe('EditorHtml', () => {
    it('hands the editor the field metadata', async () => {
        const wrapper = mountHtml();
        await nextTick();

        expect(editor(wrapper).props('id')).toBe('field-body');
        expect(editor(wrapper).props('name')).toBe('fields[body]');
    });

    it('strips the surrounding quotes Twig adds before loading the value', async () => {
        const wrapper = mountHtml({ value: '"Some <strong>html</strong>"' });
        await nextTick();

        expect(editor(wrapper).props('value')).toBe('Some <strong>html</strong>');
    });

    it('loads an unquoted value unchanged', async () => {
        const wrapper = mountHtml();
        await nextTick();

        expect(editor(wrapper).props('value')).toBe('Some <strong>html</strong>');
    });

    it('starts empty when there is no value', async () => {
        const wrapper = mountHtml({ value: '' });
        await nextTick();

        // `strip` returns undefined for a falsy value; the editor renders that
        // as an empty field.
        expect((wrapper.find('textarea').element as HTMLTextAreaElement).value).toBe('');
    });

    it('takes edits back from the editor', async () => {
        const wrapper = mountHtml();
        await nextTick();

        (editor(wrapper).vm as unknown as { type: (html: string) => void }).type('<p>Edited</p>');
        await nextTick();

        expect(editor(wrapper).props('value')).toBe('<p>Edited</p>');
    });

    it('configures the toolbar buttons', async () => {
        const wrapper = mountHtml();
        await nextTick();

        const config = editor(wrapper).props('config') as { btns: string[][] };
        expect(config.btns).toContainEqual(['strong', 'em', 'del']);
        expect(config.btns).toContainEqual(['link']);
        expect(config.btns).toContainEqual(['viewHTML']);
        expect(config.btns.flat()).not.toContain('undo');
    });
});
