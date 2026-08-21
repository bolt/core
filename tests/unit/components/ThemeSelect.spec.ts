import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import ThemeSelect from '@/editor/Components/ThemeSelect.vue';
import { withThemeLink } from '../helpers/dom';
import { captureVueErrors } from '../helpers/errors';

const themeHref = () => document.querySelector('#theme')!.getAttribute('href');

describe('ThemeSelect', () => {
    it('offers the default and light themes', () => {
        const wrapper = mount(ThemeSelect);

        expect(wrapper.findAll('.theme--name').wrappers.map(name => name.text())).toEqual(['Default', 'Light']);
    });

    it('renders a colour swatch per palette entry', () => {
        const wrapper = mount(ThemeSelect);

        const swatches = wrapper.findAll('.theme').at(0).findAll('.theme--palette span');
        expect(swatches).toHaveLength(4);
        expect(swatches.at(0).attributes('style')).toContain('background-color: rgb(26, 85, 151)');
    });

    it('swaps the stylesheet to the chosen theme', async () => {
        withThemeLink('/assets/theme-default.css');
        const wrapper = mount(ThemeSelect);

        await wrapper.findAll('.theme').at(1).trigger('click');

        expect(themeHref()).toBe('/assets/theme-light.css');
    });

    it('keeps the stylesheet directory when swapping', async () => {
        withThemeLink('/build/css/nested/theme-light.css');
        const wrapper = mount(ThemeSelect);

        await wrapper.findAll('.theme').at(0).trigger('click');

        expect(themeHref()).toBe('/build/css/nested/theme-default.css');
    });

    it('can swap back to the theme already applied', async () => {
        withThemeLink('/assets/theme-light.css');
        const wrapper = mount(ThemeSelect);

        await wrapper.findAll('.theme').at(1).trigger('click');

        expect(themeHref()).toBe('/assets/theme-light.css');
    });

    it('fails to swap when the page has no theme stylesheet', async () => {
        // Pinning current behaviour: selectTheme dereferences the result of
        // document.querySelector('#theme') with no guard, so on a page that
        // never rendered the link the click throws instead of doing nothing.
        const wrapper = mount(ThemeSelect);

        const errors = await captureVueErrors(() => wrapper.findAll('.theme').at(0).trigger('click'));

        expect(errors).toHaveLength(1);
        expect(errors[0]).toBeInstanceOf(TypeError);
    });
});
