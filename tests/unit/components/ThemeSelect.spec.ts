import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import ThemeSelect from '@/editor/Components/ThemeSelect.vue';
import { withThemeLink } from '../helpers/dom';
import { captureVueErrors } from '../helpers/errors';

const themeHref = () => document.querySelector('#theme')!.getAttribute('href');

describe('ThemeSelect', () => {
    it('offers the default and light themes', () => {
        const wrapper = mount(ThemeSelect);

        expect(wrapper.findAll('.theme--name').map(name => name.text())).toEqual(['Default', 'Light']);
    });

    it('renders a colour swatch per palette entry', () => {
        const wrapper = mount(ThemeSelect);

        const swatches = wrapper.findAll('.theme')[0].findAll('.theme--palette span');
        expect(swatches).toHaveLength(4);
        expect(swatches[0].attributes('style')).toContain('background-color: rgb(26, 85, 151)');
    });

    it('swaps the stylesheet to the chosen theme', async () => {
        withThemeLink('/assets/theme-default.css');
        const wrapper = mount(ThemeSelect);

        await wrapper.findAll('.theme')[1].trigger('click');

        expect(themeHref()).toBe('/assets/theme-light.css');
    });

    it('keeps the stylesheet directory when swapping', async () => {
        withThemeLink('/build/css/nested/theme-light.css');
        const wrapper = mount(ThemeSelect);

        await wrapper.findAll('.theme')[0].trigger('click');

        expect(themeHref()).toBe('/build/css/nested/theme-default.css');
    });

    it('can swap back to the theme already applied', async () => {
        withThemeLink('/assets/theme-light.css');
        const wrapper = mount(ThemeSelect);

        await wrapper.findAll('.theme')[1].trigger('click');

        expect(themeHref()).toBe('/assets/theme-light.css');
    });

    it('does nothing when the page has no theme stylesheet', async () => {
        const wrapper = mount(ThemeSelect);

        const errors = await captureVueErrors(() => wrapper.findAll('.theme')[0].trigger('click'));

        expect(errors).toEqual([]);
        expect(document.querySelector('#theme')).toBeNull();
    });
});
