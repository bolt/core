import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { afterEach, describe, expect, it, vi } from 'vitest';
import Language from '@/editor/Components/Language.vue';
import { multiselectStub } from '../helpers/stubs';

const locales = [
    { code: 'en', name: 'English', localizedname: 'English', flag: 'fp-gb', link: '/bolt/edit/1?edit_locale=en' },
    { code: 'nl', name: 'Dutch', localizedname: 'Nederlands', flag: 'fp-nl', link: '/bolt/edit/1?edit_locale=nl' },
];

const mountLanguage = (props: Record<string, unknown> = {}) =>
    mount(Language, {
        propsData: { label: 'Language', locales, current: 'en', ...props },
        stubs: { multiselect: multiselectStub },
    });

const picker = (wrapper: ReturnType<typeof mount>) => wrapper.findComponent(multiselectStub);
const chosen = (wrapper: ReturnType<typeof mount>) => picker(wrapper).props('value') as { code: string };

// switchLocale navigates by assigning window.location.href, which jsdom refuses
// to do. Swap in a plain object so the assignment is observable instead.
const captureNavigation = () => {
    const location = { href: '', hash: '#anchor' };
    vi.stubGlobal('location', location);
    return location;
};

afterEach(() => {
    vi.unstubAllGlobals();
});

describe('EditorLanguage', () => {
    it('renders the label', () => {
        const wrapper = mountLanguage();

        expect(wrapper.find('label').text()).toBe('Language');
    });

    it('starts on the current locale', async () => {
        const wrapper = mountLanguage({ current: 'nl' });
        await nextTick();

        expect(chosen(wrapper).code).toBe('nl');
    });

    it('falls back to the first locale when the current one is unknown', async () => {
        const wrapper = mountLanguage({ current: 'de' });
        await nextTick();

        expect(chosen(wrapper).code).toBe('en');
    });

    it('falls back to the first locale when there is no current one', async () => {
        const wrapper = mountLanguage({ current: '' });
        await nextTick();

        expect(chosen(wrapper).code).toBe('en');
    });

    it('offers every locale', async () => {
        const wrapper = mountLanguage();
        await nextTick();

        expect(picker(wrapper).props('options')).toEqual(locales);
    });

    it('renders the flag, name and code for the selected locale', async () => {
        const wrapper = mountLanguage();
        await nextTick();

        const label = wrapper.find('.stub-single-label');
        expect(label.find('span.fp').classes()).toContain('fp-gb');
        expect(label.text()).toContain('English');
        expect(label.text()).toContain('(en)');
    });

    it('renders the flag, name and code for each option', async () => {
        const wrapper = mountLanguage();
        await nextTick();

        const options = wrapper.findAll('.stub-option').wrappers;
        expect(options).toHaveLength(2);
        expect(options[1].find('span.fp').classes()).toContain('fp-nl');
        expect(options[1].text()).toContain('Dutch');
        expect(options[1].text()).toContain('(nl)');
    });

    it('navigates to the chosen locale, keeping the current anchor', async () => {
        const location = captureNavigation();
        const wrapper = mountLanguage();
        await nextTick();

        (picker(wrapper).vm as unknown as { choose: (o: unknown) => void }).choose(locales[1]);
        await nextTick();

        expect(location.href).toBe('/bolt/edit/1?edit_locale=nl#anchor');
    });

    it('stays put until a locale is actually chosen', async () => {
        const location = captureNavigation();
        mountLanguage();
        await nextTick();

        expect(location.href).toBe('');
    });
});
