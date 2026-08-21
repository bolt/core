import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Row from '@/listing/Components/Table/Row/index.vue';
import { createListingStore, type ListingSeed } from '../../helpers/store';

const labels = { actions: { button_edit: 'Edit', slug: 'Slug' } };

const record = {
    id: 42,
    status: 'published',
    createdAt: '2024-01-01T10:00:00',
    publishedAt: '2024-03-15T10:00:00',
    modifiedAt: '2024-04-01T10:00:00',
    fieldValues: { slug: 'a-page' },
    extras: {
        title: 'A page',
        excerpt: 'Some excerpt',
        editLink: '/bolt/edit/42',
        statusLink: '/bolt/status/42?dummy=1',
        duplicateLink: '/bolt/duplicate/42',
        deleteLink: '/bolt/delete/42',
        link: 'https://example.org/a-page',
        singular_name: 'Page',
        icon: 'fa-file',
        contentTypeOverviewLink: '/bolt/content/pages',
    },
};

const mountRow = (seed: ListingSeed = {}, overrides: Record<string, unknown> = {}) => {
    const testStore = createListingStore({ general: { type: 'pages' }, ...seed });
    return mount(Row, {
        localVue: testStore.localVue,
        store: testStore.store,
        propsData: { record: { ...record, ...overrides }, labels },
    });
};

describe('Listing Row', () => {
    it('renders the checkbox, details, meta and actions', () => {
        const wrapper = mountRow();

        expect(wrapper.find('.listing--checkbox').exists()).toBe(true);
        expect(wrapper.find('.is-details').exists()).toBe(true);
        expect(wrapper.find('.is-meta').exists()).toBe(true);
        expect(wrapper.find('.is-actions').exists()).toBe(true);
    });

    it('links the title to the edit page and titles it with the slug', () => {
        const wrapper = mountRow();

        const title = wrapper.find('.listing__row--item-title');
        expect(title.attributes('href')).toBe('/bolt/edit/42');
        expect(title.attributes('title')).toBe('a-page');
        expect(title.text()).toBe('A page');
    });

    it('trims a long title to 62 characters', () => {
        const wrapper = mountRow({}, { extras: { ...record.extras, title: 'x'.repeat(80) } });

        const title = wrapper.find('.listing__row--item-title').text();
        expect(title).toHaveLength(62);
        expect(title.endsWith('…')).toBe(true);
    });

    it('trims before unescaping, so entities shorten the visible title', () => {
        // `raw(trim(title, 62))` — the trim counts the escaped length, then
        // unescaping collapses each entity. "&amp;" is 5 characters going in
        // and 1 coming out, so the rendered title lands 4 short of the limit.
        const wrapper = mountRow({}, { extras: { ...record.extras, title: `Fish &amp; ${'x'.repeat(80)}` } });

        const title = wrapper.find('.listing__row--item-title').text();
        expect(title.startsWith('Fish & ')).toBe(true);
        expect(title).toHaveLength(58);
        expect(title.endsWith('…')).toBe(true);
    });

    it('unescapes the excerpt', () => {
        const wrapper = mountRow({}, { extras: { ...record.extras, excerpt: 'Fish &amp; Chips' } });

        expect(wrapper.find('.listing__row--item-title-excerpt').text()).toBe('Fish & Chips');
    });

    it('renders the feature badge only when there is one', () => {
        expect(mountRow().find('.badge').exists()).toBe(false);

        const wrapper = mountRow({}, { extras: { ...record.extras, feature: 'new' } });
        expect(wrapper.find('.badge').text()).toBe('new');
        expect(wrapper.find('.badge').classes()).toContain('badge-new');
    });

    it('hides the checkbox on the dashboard', () => {
        const wrapper = mountRow({ general: { type: 'dashboard' } });

        expect(wrapper.find('.listing--checkbox').exists()).toBe(false);
        expect(wrapper.find('.listing--container').classes()).toContain('is-dashboard');
    });

    it('reflects the current row size', async () => {
        const testStore = createListingStore({ general: { type: 'pages', rowSize: 'small' } });
        const wrapper = mount(Row, {
            localVue: testStore.localVue,
            store: testStore.store,
            propsData: { record, labels },
        });

        expect(wrapper.find('.listing__row').classes()).toContain('is-small');

        await testStore.store.dispatch('general/setRowSize', 'normal');
        await nextTick();

        expect(wrapper.find('.listing__row').classes()).toContain('is-normal');
    });

    it('renders a thumbnail at normal size when the record has an image', () => {
        const image = { thumbnail: '/thumbs/a.jpg', alt: 'A picture' };
        const wrapper = mountRow({}, { extras: { ...record.extras, image } });

        const thumbnail = wrapper.find('.is-thumbnail img');
        expect(thumbnail.attributes('src')).toBe('/thumbs/a.jpg');
        expect(thumbnail.attributes('alt')).toBe('A picture');
        expect(thumbnail.attributes('loading')).toBe('lazy');
    });

    it('omits the thumbnail at compact size', () => {
        const image = { thumbnail: '/thumbs/a.jpg', alt: 'A picture' };
        const wrapper = mountRow(
            { general: { type: 'pages', rowSize: 'small' } },
            {
                extras: { ...record.extras, image },
            },
        );

        expect(wrapper.find('.is-thumbnail').exists()).toBe(false);
    });

    it('titles the row with the first locale of a localised slug', () => {
        const wrapper = mountRow({}, { fieldValues: { slug: { nl: 'een-pagina', en: 'a-page' } } });

        expect(wrapper.find('.listing__row--item-title').attributes('title')).toBe('een-pagina');
    });

    it('tracks the sorting flag, though nothing renders it', async () => {
        // The `sorting` computed is dead code — the row template never reads it,
        // the drag handle lives in _Sorting.vue instead. Pinned through the
        // instance since there is no DOM surface, so that the Vue 3 migration
        // can see it and decide whether to drop it.
        const testStore = createListingStore({ general: { type: 'pages' } });
        const wrapper = mount(Row, {
            localVue: testStore.localVue,
            store: testStore.store,
            propsData: { record, labels },
        });
        expect((wrapper.vm as unknown as { sorting: boolean }).sorting).toBe(false);

        await testStore.store.dispatch('general/setSorting', true);
        await nextTick();

        expect((wrapper.vm as unknown as { sorting: boolean }).sorting).toBe(true);
    });

    it('titles the row with an empty slug rather than throwing', () => {
        const wrapper = mountRow({}, { fieldValues: { slug: null } });

        expect(wrapper.find('.listing__row--item-title').attributes('title')).toBe('');
    });
});
