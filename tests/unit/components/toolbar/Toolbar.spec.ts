import { mount } from '@vue/test-utils';
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

const propsData = {
    siteName: 'Example',
    menu: [],
    labels,
    urlPaths,
    backendPrefix: '/bolt/',
    isImpersonator: false,
    filterValue: '',
    avatar: '',
};

// The toolbar reads its colour from the `--admin-toolbar` custom property in
// created() and writes it to the store, so seeding the store directly would be
// overwritten. Set the property instead.
const mountToolbar = (props: Record<string, unknown> = {}, toolbarColor?: string) => {
    if (toolbarColor !== undefined) {
        document.body.style.setProperty('--admin-toolbar', toolbarColor);
    }
    const testStore = createToolbarStore();
    return {
        ...testStore,
        wrapper: mount(Toolbar, {
            localVue: testStore.localVue,
            store: testStore.store,
            propsData: { ...propsData, ...props },
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

        const hrefs = wrapper.findAll('.profile__dropdown a').wrappers.map(link => link.attributes('href'));
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

    it('reads the toolbar colour off the page on creation', () => {
        const { store, dispatched } = mountToolbar({}, '#ffffff');

        expect(dispatched('general/toolbarColor')).toEqual(['#ffffff']);
        expect(store.getters['general/toolbarColor']).toBe('#ffffff');
    });

    it('uses dark text on a light toolbar', () => {
        const { wrapper } = mountToolbar({}, '#ffffff');

        expect(wrapper.find('.admin__toolbar--body').classes()).toContain('is-light');
    });

    it('uses light text on a dark toolbar', () => {
        const { wrapper } = mountToolbar({}, '#111111');

        expect(wrapper.find('.admin__toolbar--body').classes()).toContain('is-dark');
    });

    describe('the create menu', () => {
        // `createMenu` and the `menu` prop it reads are dead code: nothing in the
        // template renders either, so there is no DOM to assert on and these
        // tests go through the instance. Covered anyway so that the behaviour is
        // pinned if the menu is ever wired back up — and so the dead code is
        // visible rather than quietly rotting.
        const createMenu = (menu: unknown[]) => {
            const { wrapper } = mountToolbar({ menu });
            return (wrapper.vm as unknown as { createMenu: { name: string }[] }).createMenu;
        };

        it('offers a named content type', () => {
            const item = { name: 'Pages', singular_name: 'Page', singleton: false, submenu: [] };

            expect(createMenu([item])).toEqual([item]);
        });

        it('leaves out a content type with no singular name', () => {
            expect(createMenu([{ name: 'Pages', singleton: false, submenu: [] }])).toEqual([]);
        });

        it('offers a singleton that has no record yet', () => {
            const empty = { name: 'Homepage', singular_name: 'Homepage', singleton: true, submenu: [] };
            const missing = { name: 'Footer', singular_name: 'Footer', singleton: true, submenu: null };

            expect(createMenu([empty, missing])).toEqual([empty, missing]);
        });

        it('leaves out a singleton that already has its record', () => {
            const filled = {
                name: 'Homepage',
                singular_name: 'Homepage',
                singleton: true,
                submenu: [{ editLink: '/bolt/edit/1' }],
            };

            expect(createMenu([filled])).toEqual([]);
        });

        it('renders nothing for it, since no template uses it', () => {
            const { wrapper } = mountToolbar({
                menu: [{ name: 'Pages', singular_name: 'Page', singleton: false, submenu: [] }],
            });

            expect(wrapper.text()).not.toContain('Pages');
        });
    });
});
