import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { afterEach, describe, expect, it } from 'vitest';
import Toolbar from '@/toolbar/Components/Toolbar.vue';
import { createToolbarStore } from '../../helpers/store';

const labels = {
    'action.stop_impersonating': 'Stop impersonating',
    'action.view_site': 'View site',
    'action.edit_profile': 'Edit profile',
    'action.logout': 'Log out',
    'general.label.search': 'Search',
    'general.greeting': 'Hello, Bob',
    'general.is_impersonator': 'impersonating',
    'listing.placeholder_search': 'Search everything',
    'listing.button_search': 'Search',
    'about.visit_bolt': 'Visit Bolt',
    'about.bolt_documentation': 'Documentation',
};

const urlPaths = {
    bolt_dashboard: '/bolt',
    bolt_profile_edit: '/bolt/profile-edit',
    bolt_logout: '/bolt/logout',
};

const props = {
    siteName: 'Example',
    menu: [],
    labels,
    urlPaths,
    backendPrefix: '/bolt/',
    isImpersonator: false,
    filterValue: '',
    avatar: '',
};

// The toolbar reads its colour from the `--admin-toolbar` custom property once
// mounted and writes it to the store, so seeding the store directly would be
// overwritten. Set the property instead.
const mountToolbar = (overrides: Record<string, unknown> = {}, toolbarColor?: string) => {
    if (toolbarColor !== undefined) {
        document.body.style.setProperty('--admin-toolbar', toolbarColor);
    }
    const testStore = createToolbarStore();
    const written = testStore.writes('general', 'toolbarColor');
    return {
        ...testStore,
        written,
        wrapper: mount(Toolbar, {
            global: { plugins: [testStore.pinia] },
            props: { ...props, ...overrides },
        }),
    };
};

afterEach(() => {
    document.body.style.removeProperty('--admin-toolbar');
});

describe('Toolbar', () => {
    it('renders the brand, site link and search form', () => {
        const { wrapper } = mountToolbar();

        expect(wrapper.find('.toolbar-item__brand img').exists()).toBe(true);
        expect(wrapper.find('.toolbar-item__site a').text()).toBe('View site');
        expect(wrapper.find('form').attributes('action')).toBe('/bolt');
        expect(wrapper.find('#global-search').attributes('placeholder')).toBe('Search everything');
    });

    it('carries the current filter into the search box', () => {
        const { wrapper } = mountToolbar({ filterValue: 'kittens' });

        // `:value` binds the DOM property, not the html attribute.
        expect((wrapper.find('#global-search').element as HTMLInputElement).value).toBe('kittens');
    });

    it('renders the profile menu links', () => {
        const { wrapper } = mountToolbar();

        const hrefs = wrapper.findAll('.profile__dropdown a').map(link => link.attributes('href'));
        expect(hrefs).toEqual(['/bolt/profile-edit', '/bolt/logout', 'https://boltcms.io/', 'https://docs.bolt.cm/']);
    });

    it('falls back to a user icon when there is no avatar', () => {
        const { wrapper } = mountToolbar();

        expect(wrapper.find('.toolbar-item__profile i.fa-user').exists()).toBe(true);
        expect(wrapper.find('.toolbar-item__profile img').exists()).toBe(false);
    });

    it('renders the avatar when there is one', () => {
        const { wrapper } = mountToolbar({ avatar: '/avatars/bob.png' });

        const avatar = wrapper.find('.toolbar-item__profile img');
        expect(avatar.attributes('src')).toBe('/avatars/bob.png');
        expect(avatar.attributes('alt')).toBe('User avatar');
        expect(wrapper.find('.toolbar-item__profile i.fa-user').exists()).toBe(false);
    });

    it('hides the impersonation banner by default', () => {
        const { wrapper } = mountToolbar();

        expect(wrapper.find('.toolbar-impersonation').exists()).toBe(false);
        expect(wrapper.text()).not.toContain('impersonating');
    });

    it('offers a way out when impersonating', () => {
        const { wrapper } = mountToolbar({ isImpersonator: true });

        const exit = wrapper.find('.toolbar-impersonation a');
        expect(exit.attributes('href')).toBe('/bolt?_switch_user=_exit');
        expect(exit.text()).toBe('Stop impersonating');
        expect(wrapper.find('.toolbar-item__profile').text()).toContain('impersonating');
    });

    it('reads the toolbar colour off the page once mounted', () => {
        const { stores, written } = mountToolbar({}, '#ffffff');

        expect(written).toEqual(['#ffffff']);
        expect(stores.general.toolbarColor).toBe('#ffffff');
    });

    it('uses dark text on a light toolbar', async () => {
        const { wrapper } = mountToolbar({}, '#ffffff');
        await nextTick();

        expect(wrapper.find('.admin__toolbar--body').classes()).toContain('is-light');
    });

    it('uses light text on a dark toolbar', async () => {
        const { wrapper } = mountToolbar({}, '#111111');
        await nextTick();

        expect(wrapper.find('.admin__toolbar--body').classes()).toContain('is-dark');
    });

    it('renders nothing for the content type menu it is given', () => {
        // Twig still passes `menu`, but the toolbar has no create menu to put it in.
        const { wrapper } = mountToolbar({
            menu: [{ name: 'Pages', singular_name: 'Page', singleton: false, submenu: [] }],
        });

        expect(wrapper.text()).not.toContain('Pages');
    });
});
