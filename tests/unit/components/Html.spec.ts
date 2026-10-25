import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import jQuery from 'jquery';
import { describe, expect, it, vi } from 'vitest';
import Html from '@/editor/Components/Html.vue';
import { trumbowygStub } from '../helpers/stubs';
import { contextLoader } from '../helpers/webpack-context';

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

    describe('toolbar language', () => {
        type TrumbowygLangs = Record<string, Record<string, string> | undefined>;
        const langs = () => (jQuery as unknown as { trumbowyg: { langs: TrumbowygLangs } }).trumbowyg.langs;

        // Trumbowyg is only rendered once its language is known, and loading one
        // is a dynamic import, so wait for the editor rather than for a tick.
        const mountReady = async (props: Record<string, unknown> = {}) => {
            const wrapper = mountHtml(props);
            await vi.waitFor(() => expect(editor(wrapper).exists()).toBe(true));

            return wrapper;
        };

        const lang = (wrapper: ReturnType<typeof mount>) => (editor(wrapper).props('config') as { lang: string }).lang;

        it('stays in English without a locale, and loads nothing', async () => {
            const load = vi.spyOn(contextLoader, 'load');
            const wrapper = await mountReady();

            expect(lang(wrapper)).toBe('en');
            expect(load).not.toHaveBeenCalled();
        });

        it('loads the translation matching the locale', async () => {
            const load = vi.spyOn(contextLoader, 'load');
            const wrapper = await mountReady({ locale: 'cs' });

            expect(lang(wrapper)).toBe('cs');
            expect(load).toHaveBeenCalledOnce();
            expect(load.mock.calls[0][0]).toBe('./cs.min.js');
            expect(langs().cs?.viewHTML).toBe('Zobrazit HTML');
        });

        it('renders the editor only once the language is loaded', async () => {
            const wrapper = mountHtml({ locale: 'cs' });
            await nextTick();

            expect(editor(wrapper).exists()).toBe(false);

            await vi.waitFor(() => expect(editor(wrapper).exists()).toBe(true));
            expect(lang(wrapper)).toBe('cs');
        });

        it.each([
            ['de_AT', 'de'],
            ['pt_BR', 'pt_br'],
            ['pt-BR', 'pt_br'],
            ['es_MX', 'es'],
            ['sr_Latn_RS', 'rs_latin'],
            ['sr_Cyrl_RS', 'rs'],
        ])('falls back from %s to the closest translation, %s', async (locale, expected) => {
            const wrapper = await mountReady({ locale });

            expect(lang(wrapper)).toBe(expected);
        });

        it.each([
            ['uk', 'ua'],
            ['sr', 'rs'],
            ['be', 'by'],
            ['fil', 'ph'],
            ['tl', 'ph'],
            ['no', 'nb'],
            ['nn', 'nb'],
            ['zh', 'zh_cn'],
            ['zh_Hans', 'zh_cn'],
            ['zh_Hant', 'zh_tw'],
            ['zh_HK', 'zh_tw'],
            ['zh_MO', 'zh_tw'],
        ])('maps %s to the Trumbowyg name %s', async (locale, expected) => {
            const wrapper = await mountReady({ locale });

            expect(lang(wrapper)).toBe(expected);
        });

        // Trumbowyg's `my` file is meant to be Malay, while ISO `my` is Burmese,
        // and the file registers itself as `id`, so Malay has no usable file either.
        it.each(['my', 'my_MM', 'ms', 'xx', 'en_GB'])('stays in English for %s, and loads nothing', async locale => {
            const load = vi.spyOn(contextLoader, 'load');
            const wrapper = await mountReady({ locale });

            expect(lang(wrapper)).toBe('en');
            expect(load).not.toHaveBeenCalled();
        });

        it('stays in English when the translation fails to load', async () => {
            vi.spyOn(contextLoader, 'load').mockRejectedValueOnce(new Error('Loading chunk failed'));
            const wrapper = await mountReady({ locale: 'fr' });

            expect(lang(wrapper)).toBe('en');
        });

        it('stays in English when the translation registers no language under its name', async () => {
            vi.spyOn(contextLoader, 'load').mockResolvedValueOnce({});
            const wrapper = await mountReady({ locale: 'vi' });

            expect(lang(wrapper)).toBe('en');
        });

        it.each(['ar', 'he', 'fa'])('uses the %s labels but keeps the editing direction', async locale => {
            const wrapper = await mountReady({ locale });

            expect(lang(wrapper)).toBe(locale);
            expect(langs()[locale]?.viewHTML).toBeTruthy();
            expect(langs()[locale]).not.toHaveProperty('_dir');
        });
    });
});
