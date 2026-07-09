import { mount } from '@vue/test-utils';
import ThemeSelect from '@/editor/Components/ThemeSelect.vue';
import { describe, it, expect, beforeEach, afterEach } from 'vitest';

describe('ThemeSelect Component', () => {
    beforeEach(() => {
        document.head.innerHTML = '<link id="theme" rel="stylesheet" href="/assets/css/theme-default.css" />';
    });

    afterEach(() => {
        document.head.innerHTML = '';
    });

    it('lists the available themes with their palettes', () => {
        const wrapper = mount(ThemeSelect);

        const themes = wrapper.findAll('.theme');
        expect(themes).toHaveLength(2);
        expect(themes[0].find('.theme--name').text()).toBe('Default');
        expect(themes[1].find('.theme--name').text()).toBe('Light');
        expect(themes[0].findAll('.theme--palette span')).toHaveLength(4);
    });

    it('switches the theme stylesheet when a theme is clicked', async () => {
        const wrapper = mount(ThemeSelect);

        await wrapper.findAll('.theme')[1].trigger('click');

        expect(document.querySelector<HTMLLinkElement>('#theme')?.getAttribute('href')).toBe(
            '/assets/css/theme-light.css',
        );
    });

    it('does not fail when the theme stylesheet is missing', async () => {
        document.head.innerHTML = '';
        const wrapper = mount(ThemeSelect);

        await wrapper.findAll('.theme')[1].trigger('click');

        expect(document.querySelector<HTMLLinkElement>('#theme')).toBeNull();
    });

    it('does not change a theme stylesheet without an href', async () => {
        document.head.innerHTML = '<link id="theme" rel="stylesheet" />';
        const wrapper = mount(ThemeSelect);

        await wrapper.findAll('.theme')[1].trigger('click');

        expect(document.querySelector<HTMLLinkElement>('#theme')?.hasAttribute('href')).toBe(false);
    });
});
