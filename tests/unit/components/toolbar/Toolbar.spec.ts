import type { Pinia } from 'pinia';
import { mount } from '@vue/test-utils';
import Toolbar from '@/toolbar/Components/Toolbar.vue';
import { describe, it, expect, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useGeneralStore } from '@/toolbar/store';

describe('Toolbar Component', () => {
    let generalStore: ReturnType<typeof useGeneralStore>;
    let pinia: Pinia;

    beforeEach(() => {
        pinia = createPinia();
        setActivePinia(pinia);
        generalStore = useGeneralStore();
    });

    const defaultProps = {
        siteName: 'Test Site',
        menu: [],
        labels: {
            'action.stop_impersonating': 'Stop Impersonating',
            'action.view_site': 'View Site',
            'general.label.search': 'Search',
            'listing.placeholder_search': 'Type here...',
            'listing.button_search': 'Search btn',
            'general.greeting': 'Hi User',
            'general.is_impersonator': 'Impersonating',
            'action.edit_profile': 'Edit Profile',
            'action.logout': 'Logout',
            'about.visit_bolt': 'Visit Bolt',
            'about.bolt_documentation': 'Docs',
        },
        urlPaths: {
            bolt_dashboard: '/dashboard',
            bolt_profile_edit: '/profile',
            bolt_logout: '/logout',
        },
        backendPrefix: 'bolt',
        isImpersonator: false,
        filterValue: 'query',
        avatar: '/avatar.jpg',
    };

    const mountComponent = (propsOverride = {}) => {
        return mount(Toolbar, {
            props: { ...defaultProps, ...propsOverride },
            global: {
                plugins: [pinia],
            },
        });
    };

    it('renders basic elements correctly', () => {
        const wrapper = mountComponent();

        const logo = wrapper.find('img[alt="⚙️ Bolt"]');
        expect(logo.exists()).toBe(true);
        expect(logo.attributes('src')).toBeTruthy();
        expect(logo.attributes('height')).toBe('26');
        expect(wrapper.find('.toolbar-item__site a').attributes('href')).toBe('/');

        // Search form
        const searchInput = wrapper.find('input#global-search');
        expect(searchInput.attributes('placeholder')).toBe('Type here...');
        expect((searchInput.element as HTMLInputElement).value).toBe('query');
        expect(wrapper.find('form').attributes('action')).toBe('/dashboard');

        // Profile Dropdown
        const profileBtn = wrapper.find('button.profile__dropdown-toggler');
        expect(profileBtn.text()).toContain('Hi User');
        expect(profileBtn.find('img').attributes('src')).toBe('/avatar.jpg');
    });

    it('renders impersonation link if isImpersonator is true', () => {
        const wrapper = mountComponent({ isImpersonator: true });

        const impDiv = wrapper.find('.toolbar-impersonation');
        expect(impDiv.exists()).toBe(true);
        expect(impDiv.find('a').attributes('href')).toBe('/dashboard?_switch_user=_exit');
        expect(impDiv.text()).toContain('Stop Impersonating');

        // Also check greeting badge
        const profileBtn = wrapper.find('button.profile__dropdown-toggler');
        expect(profileBtn.text()).toContain('(Impersonating)');
    });

    it('shows default user icon if no avatar provided', () => {
        const wrapper = mountComponent({ avatar: null });

        const profileBtn = wrapper.find('button.profile__dropdown-toggler');
        expect(profileBtn.find('img').exists()).toBe(false);
        expect(profileBtn.find('i.fa-user').exists()).toBe(true);
    });

    it('computes contrast correctly based on store toolbarColor', async () => {
        const wrapper = mountComponent();
        const root = wrapper.find('.admin__toolbar--body');

        // Store is empty initially or holds #ffffff etc. based on component created() reading from CSS
        // we'll mock tinycolor light behavior by setting the store manually.
        generalStore.toolbarColor = '#ffffff'; // Light color
        await wrapper.vm.$nextTick();

        // In the unmigrated component, contrast() is a computed property that relies on the store.
        expect(root.classes()).toContain('is-light');

        generalStore.toolbarColor = '#000000'; // Dark color
        await wrapper.vm.$nextTick();
        expect(root.classes()).toContain('is-dark');
    });
});
