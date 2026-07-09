import { mount } from '@vue/test-utils';
import SubMenu from '@/sidebar/Components/Menu/_SubMenu.vue';
import { describe, it, expect } from 'vitest';

describe('SubMenu Component', () => {
    const defaultProps = {
        item: {
            name: 'Posts',
            link_new: '/new/post',
            link_listing: '/listing/post',
            icon: 'fa-post',
            submenu: [
                {
                    name: 'Post 1',
                    editLink: '/edit/1',
                    icon: 'fa-file',
                },
                {
                    name: null,
                    editLink: '/edit/2',
                    icon: 'fa-file',
                },
            ],
        },
        labels: {
            'action.new': 'New',
            'action.view': 'View',
        },
    };

    it('renders actions correctly', () => {
        const wrapper = mount(SubMenu, { props: defaultProps });

        const actionLinks = wrapper.findAll('.link--actions a');
        expect(actionLinks).toHaveLength(2);

        // New Link
        const newLink = actionLinks[0];
        expect(newLink.attributes('href')).toBe('/new/post');
        expect(newLink.text()).toContain('New');

        // View Link
        const viewLink = actionLinks[1];
        expect(viewLink.attributes('href')).toBe('/listing/post');
        expect(viewLink.text()).toContain('View Posts');
        expect(viewLink.find('.fas').classes()).toContain('fa-post');
    });

    it('does not render new link if link_new is null', () => {
        const props = {
            ...defaultProps,
            item: { ...defaultProps.item, link_new: null },
        };
        const wrapper = mount(SubMenu, { props });

        const actionLinks = wrapper.findAll('.link--actions a');
        expect(actionLinks).toHaveLength(1);
        expect(actionLinks[0].text()).toContain('View Posts');
    });

    it('renders submenu items correctly', () => {
        const wrapper = mount(SubMenu, { props: defaultProps });

        const subItems = wrapper.findAll('li:not(.link--actions) a');
        expect(subItems).toHaveLength(2);

        // Named Item
        const item1 = subItems[0];
        expect(item1.attributes('href')).toBe('/edit/1');
        expect(item1.find('span').html()).toContain('Post 1');
        expect(item1.find('.fas').classes()).toContain('fa-file');

        // Untitled Item
        const item2 = subItems[1];
        expect(item2.attributes('href')).toBe('/edit/2');
        expect(item2.find('em').text()).toBe('(Untitled)');
    });

    it('handles empty submenu', () => {
        const props = {
            ...defaultProps,
            item: { ...defaultProps.item, submenu: null },
        };
        const wrapper = mount(SubMenu, { props });

        const subItems = wrapper.findAll('li:not(.link--actions) a');
        expect(subItems).toHaveLength(0);
    });
});
