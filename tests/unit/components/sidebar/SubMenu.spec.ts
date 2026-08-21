import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import SubMenu from '@/sidebar/Components/Menu/_SubMenu.vue';

const labels = { 'action.new': 'New', 'action.view': 'View' };

const item = {
    name: 'Pages',
    icon: 'fa-file',
    link_new: '/bolt/new/pages',
    link_listing: '/bolt/content/pages',
    submenu: [
        { name: 'About', icon: 'fa-file', editLink: '/bolt/edit/1' },
        { name: 'Contact', icon: 'fa-file', editLink: '/bolt/edit/2' },
    ],
};

describe('Sidebar SubMenu', () => {
    it('renders the new and listing actions', () => {
        const wrapper = mount(SubMenu, { propsData: { item, labels } });

        const actions = wrapper.findAll('.link--actions a');
        expect(actions.at(0).attributes('href')).toBe('/bolt/new/pages');
        expect(actions.at(0).text()).toBe('New');
        expect(actions.at(1).attributes('href')).toBe('/bolt/content/pages');
        expect(actions.at(1).text()).toBe('View Pages');
    });

    it('omits the new action when there is no new link', () => {
        const wrapper = mount(SubMenu, {
            propsData: { item: { ...item, link_new: null }, labels },
        });

        const actions = wrapper.findAll('.link--actions a');
        expect(actions).toHaveLength(1);
        expect(actions.at(0).attributes('href')).toBe('/bolt/content/pages');
    });

    it('renders a link per submenu record', () => {
        const wrapper = mount(SubMenu, { propsData: { item, labels } });

        const records = wrapper.findAll('li:not(.link--actions) a');
        expect(records).toHaveLength(2);
        expect(records.at(0).attributes('href')).toBe('/bolt/edit/1');
        expect(records.at(0).text()).toBe('About');
        expect(records.at(1).text()).toBe('Contact');
    });

    it('falls back to "(Untitled)" for a record with no name', () => {
        const wrapper = mount(SubMenu, {
            propsData: {
                item: { ...item, submenu: [{ name: '', icon: 'fa-file', editLink: '/bolt/edit/3' }] },
                labels,
            },
        });

        expect(wrapper.find('li:not(.link--actions) em').text()).toBe('(Untitled)');
    });

    it('renders record names as html, since Twig sends them escaped', () => {
        const wrapper = mount(SubMenu, {
            propsData: {
                item: { ...item, submenu: [{ name: 'A <em>fancy</em> title', icon: 'fa-file', editLink: '/x' }] },
                labels,
            },
        });

        expect(wrapper.find('li:not(.link--actions) span em').text()).toBe('fancy');
    });

    it('renders only the actions when the submenu is empty', () => {
        const wrapper = mount(SubMenu, { propsData: { item: { ...item, submenu: [] }, labels } });

        expect(wrapper.findAll('li:not(.link--actions)')).toHaveLength(0);
    });
});
