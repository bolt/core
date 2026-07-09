import { mount } from '@vue/test-utils';
import Actions from '@/listing/Components/Table/Row/_Actions.vue';
import { describe, it, expect } from 'vitest';

describe('Actions Component', () => {
    const defaultProps = {
        type: 'articles',
        record: {
            status: 'draft',
            createdAt: '2023-01-01 10:00:00',
            publishedAt: '2023-01-02 10:00:00',
            modifiedAt: '2023-01-03 10:00:00',
            fieldValues: {
                slug: 'my-slug',
            },
            extras: {
                editLink: '/edit/1',
                link: '/view/1',
                statusLink: '/status/1?dummy=1',
                duplicateLink: '/duplicate/1',
                deleteLink: '/delete/1',
                singular_name: 'Article',
            },
        },
        labels: {
            button_edit: 'Edit',
            view_on_site: 'View on site',
            status_to_publish: 'Publish',
            status_to_held: 'Hold',
            status_to_draft: 'Draft',
            duplicate: 'Duplicate',
            delete: 'Delete',
            slug: 'Slug',
            created_on: 'Created',
            published_on: 'Published',
            last_modified_on: 'Modified',
        },
    };

    const mountComponent = (propsOverride = {}) => {
        return mount(Actions, {
            props: { ...defaultProps, ...propsOverride },
        });
    };

    it('renders edit button correctly', () => {
        const wrapper = mountComponent();

        const editLink = wrapper.find('a.btn.btn-secondary');
        expect(editLink.attributes('href')).toBe('/edit/1');
        expect(editLink.text()).toContain('Edit');
    });

    it('shows publish and hold options when status is draft', () => {
        const wrapper = mountComponent();

        const dropdownItems = wrapper.findAll('.dropdown-item');
        const hrefs = dropdownItems.map(item => item.attributes('href'));

        expect(hrefs).toContain('/status/1?dummy=1&status=published');
        expect(hrefs).toContain('/status/1?dummy=1&status=held');
        expect(hrefs).not.toContain('/status/1?dummy=1&status=draft');
        expect(hrefs).not.toContain('/view/1');
    });

    it('shows view and hold and draft options when status is published', () => {
        const wrapper = mountComponent({
            record: {
                ...defaultProps.record,
                status: 'published',
            },
        });

        const dropdownItems = wrapper.findAll('.dropdown-item');
        const hrefs = dropdownItems.map(item => item.attributes('href'));

        expect(hrefs).toContain('/view/1');
        expect(hrefs).toContain('/status/1?dummy=1&status=held');
        expect(hrefs).toContain('/status/1?dummy=1&status=draft');
        expect(hrefs).not.toContain('/status/1?dummy=1&status=published');
    });

    it('shows publish and draft options when status is held', () => {
        const wrapper = mountComponent({
            record: {
                ...defaultProps.record,
                status: 'held',
            },
        });

        const dropdownItems = wrapper.findAll('.dropdown-item');
        const hrefs = dropdownItems.map(item => item.attributes('href'));

        expect(hrefs).toContain('/status/1?dummy=1&status=published');
        expect(hrefs).toContain('/status/1?dummy=1&status=draft');
        expect(hrefs).not.toContain('/status/1?dummy=1&status=held');
    });

    it('renders meta information correctly', () => {
        const wrapper = mountComponent();

        const texts = wrapper.findAll('.dropdown-item-text');
        expect(texts[0].text()).toContain('Slug');
        expect(texts[0].text()).toContain('my-slug');
        expect(texts[1].text()).toContain('Created');
        expect(texts[2].text()).toContain('Published');
        expect(texts[3].text()).toContain('Modified');
    });

    it('handles null slug', () => {
        const wrapper = mountComponent({
            record: {
                ...defaultProps.record,
                fieldValues: { slug: null },
            },
        });

        const texts = wrapper.findAll('.dropdown-item-text');
        const slugCode = texts[0].find('code');
        expect(slugCode.text()).toBe('');
    });

    it('handles localized slug', () => {
        const wrapper = mountComponent({
            record: {
                ...defaultProps.record,
                fieldValues: { slug: { en: 'en-slug', nl: 'nl-slug' } },
            },
        });

        const texts = wrapper.findAll('.dropdown-item-text');
        const slugCode = texts[0].find('code');
        expect(slugCode.text()).toBe('en-slug');
    });
});
