import { mount } from '@vue/test-utils';
import MenuIndex from '@/sidebar/Components/Menu/index.vue';
import { describe, it, expect } from 'vitest';

describe('Sidebar Menu Component', () => {
    const defaultProps = {
        menu: [
            { type: 'separator', name: 'Separator 1' },
            {
                type: 'item',
                name: 'Item 1',
                link: '/item-1',
                icon: 'fa-icon',
                submenu: null,
            },
            {
                type: 'item',
                name: 'Item with Submenu',
                link: '/item-with-submenu',
                icon: 'fa-icon-2',
                submenu: [{ name: 'Sub 1', link: '/sub-1' }],
            },
            {
                type: 'item',
                name: 'Singleton Empty Submenu',
                icon: 'fa-singleton',
                singleton: true,
                submenu: [],
                link_new: '/singleton-new',
            },
            {
                type: 'item',
                name: 'Singleton With Submenu',
                icon: 'fa-singleton',
                singleton: true,
                submenu: [{ editLink: '/singleton-edit' }],
            },
        ],
        labels: { view: 'View' },
    };

    const mountComponent = () => {
        return mount(MenuIndex, {
            props: defaultProps,
            global: {
                stubs: {
                    'sub-menu': true,
                },
            },
        });
    };

    it('renders separators correctly', () => {
        const wrapper = mountComponent();

        const separators = wrapper.findAll('.admin__sidebar--separator');
        expect(separators).toHaveLength(1);
        expect(separators[0].text()).toBe('Separator 1');
    });

    it('renders normal items correctly', () => {
        const wrapper = mountComponent();

        const links = wrapper.findAll('.admin__sidebar--link');

        // Item 1
        const item1 = links[0];
        expect(item1.attributes('href')).toBe('/item-1');
        expect(item1.classes()).not.toContain('has-menu');
        expect(item1.find('.link--text').text()).toBe('Item 1');
        expect(item1.find('.link--icon').classes()).toContain('fa-icon');
        expect(item1.findComponent({ name: 'sub-menu' }).exists()).toBe(false);

        // Item with Submenu
        const itemSubmenu = links[1];
        expect(itemSubmenu.attributes('href')).toBe('/item-with-submenu');
        expect(itemSubmenu.classes()).toContain('has-menu');
        expect(itemSubmenu.find('.link--text').text()).toBe('Item with Submenu');
        expect(itemSubmenu.find('.link--icon').classes()).toContain('fa-icon-2');
        expect(itemSubmenu.find('.link--caret').exists()).toBe(true);
        expect(itemSubmenu.findComponent({ name: 'sub-menu' }).exists()).toBe(true);
    });

    it('renders singleton with empty submenu correctly', () => {
        const wrapper = mountComponent();
        const links = wrapper.findAll('.admin__sidebar--link');

        const singletonEmpty = links[2];
        expect(singletonEmpty.attributes('href')).toBe('/singleton-new');
        expect(singletonEmpty.find('.link--text').text()).toBe('Singleton Empty Submenu');
    });

    it('renders singleton with submenu correctly', () => {
        const wrapper = mountComponent();
        const links = wrapper.findAll('.admin__sidebar--link');

        const singletonWithSub = links[3];
        expect(singletonWithSub.attributes('href')).toBe('/singleton-edit');
        expect(singletonWithSub.find('.link--text').text()).toBe('Singleton With Submenu');
    });
});
