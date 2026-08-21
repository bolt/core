import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import { DateTime } from 'luxon';
import Actions from '@/listing/Components/Table/Row/_Actions.vue';

const labels = {
    button_edit: 'Edit',
    view_on_site: 'View on site',
    status_to_publish: 'Publish',
    status_to_held: 'Hold',
    status_to_draft: 'Make draft',
    duplicate: 'Duplicate',
    delete: 'Delete',
    slug: 'Slug',
    created_on: 'Created',
    published_on: 'Published',
    last_modified_on: 'Modified',
};

const record = {
    id: 42,
    status: 'published',
    createdAt: '2024-01-01T10:00:00',
    publishedAt: '2024-03-15T10:00:00',
    modifiedAt: '2024-04-01T10:00:00',
    fieldValues: { slug: 'a-page' },
    extras: {
        editLink: '/bolt/edit/42',
        link: 'https://example.org/a-page',
        statusLink: '/bolt/status/42?dummy=1',
        duplicateLink: '/bolt/duplicate/42',
        deleteLink: '/bolt/delete/42',
        singular_name: 'Page',
    },
};

const mountActions = (overrides: Record<string, unknown> = {}) =>
    mount(Actions, { propsData: { type: 'pages', record: { ...record, ...overrides }, labels } });

const dropdownHrefs = (wrapper: ReturnType<typeof mount>) =>
    wrapper.findAll('.edit-actions__dropdown a').wrappers.map(link => link.attributes('href'));

describe('Listing row Actions', () => {
    it('renders the edit button', () => {
        const wrapper = mountActions();

        const edit = wrapper.find('a.btn-secondary');
        expect(edit.attributes('href')).toBe('/bolt/edit/42');
        expect(edit.text()).toBe('Edit');
    });

    it('offers a public link and the two other statuses for a published record', () => {
        const wrapper = mountActions();

        expect(wrapper.text()).toContain('View on site');
        expect(dropdownHrefs(wrapper)).toContain('https://example.org/a-page');
        expect(dropdownHrefs(wrapper)).toContain('/bolt/status/42?dummy=1&status=held');
        expect(dropdownHrefs(wrapper)).toContain('/bolt/status/42?dummy=1&status=draft');
        expect(dropdownHrefs(wrapper)).not.toContain('/bolt/status/42?dummy=1&status=published');
    });

    it('offers publish and hold for a draft record, and no public link', () => {
        const wrapper = mountActions({ status: 'draft' });

        expect(wrapper.text()).not.toContain('View on site');
        expect(dropdownHrefs(wrapper)).toContain('/bolt/status/42?dummy=1&status=published');
        expect(dropdownHrefs(wrapper)).toContain('/bolt/status/42?dummy=1&status=held');
        expect(dropdownHrefs(wrapper)).not.toContain('/bolt/status/42?dummy=1&status=draft');
    });

    it('offers publish and draft for a held record', () => {
        const wrapper = mountActions({ status: 'held' });

        expect(dropdownHrefs(wrapper)).toContain('/bolt/status/42?dummy=1&status=published');
        expect(dropdownHrefs(wrapper)).toContain('/bolt/status/42?dummy=1&status=draft');
        expect(dropdownHrefs(wrapper)).not.toContain('/bolt/status/42?dummy=1&status=held');
    });

    it('renders the duplicate and delete actions with the content type name', () => {
        const wrapper = mountActions();

        expect(wrapper.text()).toContain('Duplicate Page');
        expect(wrapper.text()).toContain('Delete Page');
        expect(dropdownHrefs(wrapper)).toContain('/bolt/duplicate/42');
        expect(dropdownHrefs(wrapper)).toContain('/bolt/delete/42');
    });

    it('renders the record dates', () => {
        const wrapper = mountActions();

        const text = wrapper.text();
        expect(text).toContain(DateTime.fromISO('2024-01-01T10:00:00').toLocaleString(DateTime.DATETIME_MED));
        expect(text).toContain(DateTime.fromISO('2024-03-15T10:00:00').toLocaleString(DateTime.DATETIME_MED));
        expect(text).toContain(DateTime.fromISO('2024-04-01T10:00:00').toLocaleString(DateTime.DATETIME_MED));
    });

    it('renders a plain string slug', () => {
        const wrapper = mountActions();

        expect(wrapper.find('code').text()).toBe('a-page');
        expect(wrapper.find('code').attributes('title')).toBe('a-page');
    });

    it('renders the first locale of a localised slug', () => {
        const wrapper = mountActions({ fieldValues: { slug: { en: 'a-page', nl: 'een-pagina' } } });

        expect(wrapper.find('code').text()).toBe('a-page');
    });

    it('renders an empty slug as empty rather than throwing', () => {
        const wrapper = mountActions({ fieldValues: { slug: null } });

        expect(wrapper.find('code').text()).toBe('');
    });

    it('trims a long slug for display but keeps the full value in the title', () => {
        const long = 'a-very-long-slug-that-will-not-fit-in-the-dropdown';
        const wrapper = mountActions({ fieldValues: { slug: long } });

        expect(wrapper.find('code').text()).toHaveLength(24);
        expect(wrapper.find('code').text().endsWith('…')).toBe(true);
        expect(wrapper.find('code').attributes('title')).toBe(long);
    });

    it.skip('hides the status links when the record has no status link', () => {
        // Only passes after fcb39369 ("Hide listing status links when statusLink
        // is unavailable"). Today the links are always rendered, so a record
        // without a statusLink gets href="undefined&status=published".
        const wrapper = mountActions({ status: 'draft', extras: { ...record.extras, statusLink: undefined } });

        expect(dropdownHrefs(wrapper)).not.toContain('undefined&status=published');
    });
});
