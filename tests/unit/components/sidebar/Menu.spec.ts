import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import Menu from '@/sidebar/Components/Menu/index.vue';

const labels = { 'action.new': 'New', 'action.view': 'View' };

describe('Sidebar Menu', () => {
    it('renders a separator as a heading rather than a link', () => {
        const wrapper = mount(Menu, {
            propsData: { menu: [{ type: 'separator', name: 'Content' }], labels },
        });

        expect(wrapper.find('.admin__sidebar--separator').text()).toBe('Content');
        expect(wrapper.find('a').exists()).toBe(false);
    });

    it('renders a plain item as a link with its icon', () => {
        const wrapper = mount(Menu, {
            propsData: { menu: [{ name: 'Dashboard', icon: 'fa-home', link: '/bolt', submenu: null }], labels },
        });

        const link = wrapper.find('a.admin__sidebar--link');
        expect(link.attributes('href')).toBe('/bolt');
        expect(link.find('.link--text').text()).toBe('Dashboard');
        expect(link.find('i.link--icon').classes()).toContain('fa-home');
        expect(link.classes()).not.toContain('has-menu');
    });

    it('renders a submenu and caret for an item that has one', () => {
        const wrapper = mount(Menu, {
            propsData: {
                menu: [
                    {
                        name: 'Pages',
                        icon: 'fa-file',
                        link: '/bolt/content/pages',
                        link_new: '/bolt/new/pages',
                        link_listing: '/bolt/content/pages',
                        submenu: [{ name: 'About', icon: 'fa-file', editLink: '/bolt/edit/1' }],
                    },
                ],
                labels,
            },
        });

        expect(wrapper.find('a.admin__sidebar--link').classes()).toContain('has-menu');
        expect(wrapper.find('i.link--caret').exists()).toBe(true);
        expect(wrapper.find('ul.link--menu').exists()).toBe(true);
    });

    it('points a singleton at its first record rather than at the listing', () => {
        const wrapper = mount(Menu, {
            propsData: {
                menu: [
                    {
                        name: 'Homepage',
                        icon: 'fa-home',
                        singleton: true,
                        link: '/bolt/content/homepage',
                        link_new: '/bolt/new/homepage',
                        submenu: [{ name: 'Homepage', icon: 'fa-home', editLink: '/bolt/edit/9' }],
                    },
                ],
                labels,
            },
        });

        expect(wrapper.find('a.admin__sidebar--link').attributes('href')).toBe('/bolt/edit/9');
    });

    it('points a singleton with no record at the new-record link', () => {
        const wrapper = mount(Menu, {
            propsData: {
                menu: [
                    {
                        name: 'Homepage',
                        icon: 'fa-home',
                        singleton: true,
                        link: '/bolt/content/homepage',
                        link_new: '/bolt/new/homepage',
                        submenu: [],
                    },
                ],
                labels,
            },
        });

        expect(wrapper.find('a.admin__sidebar--link').attributes('href')).toBe('/bolt/new/homepage');
    });

    it('renders one list item per menu entry', () => {
        const wrapper = mount(Menu, {
            propsData: {
                menu: [
                    { type: 'separator', name: 'Content' },
                    { name: 'Dashboard', icon: 'fa-home', link: '/bolt', submenu: null },
                ],
                labels,
            },
        });

        expect(wrapper.findAll('ul.admin__sidebar--menu > li')).toHaveLength(2);
    });
});
