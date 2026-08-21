import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import { DateTime } from 'luxon';
import Meta from '@/listing/Components/Table/Row/_Meta.vue';

const record = {
    id: 42,
    status: 'published',
    publishedAt: '2024-03-15T10:00:00',
    createdAt: '2024-01-01T10:00:00',
    authorName: 'Bob den Otter',
    extras: {
        icon: 'fa-file',
        singular_name: 'Page',
        contentTypeOverviewLink: '/bolt/content/pages',
    },
};

const rendered = (iso: string) => DateTime.fromISO(iso).toLocaleString();

describe('Listing row Meta', () => {
    it('renders the status marker and the published date', () => {
        const wrapper = mount(Meta, { propsData: { type: 'pages', size: 'normal', record } });

        const status = wrapper.find('.status');
        expect(status.classes()).toContain('is-published');
        expect(status.attributes('title')).toBe('published');
        expect(wrapper.text()).toContain(rendered('2024-03-15T10:00:00'));
    });

    it('falls back to the created date when there is no published date', () => {
        const wrapper = mount(Meta, {
            propsData: { type: 'pages', size: 'normal', record: { ...record, publishedAt: null } },
        });

        expect(wrapper.text()).toContain(rendered('2024-01-01T10:00:00'));
    });

    it('renders the author and content type at normal size', () => {
        const wrapper = mount(Meta, { propsData: { type: 'pages', size: 'normal', record } });

        expect(wrapper.text()).toContain('Bob den Otter');
        expect(wrapper.text()).toContain('Page');
        expect(wrapper.text()).toContain('№ 42');
        expect(wrapper.findAll('.listing__row--list li')).toHaveLength(3);
    });

    it('shows only the date row at compact size', () => {
        const wrapper = mount(Meta, { propsData: { type: 'pages', size: 'compact', record } });

        expect(wrapper.findAll('.listing__row--list li')).toHaveLength(1);
        expect(wrapper.text()).not.toContain('Bob den Otter');
    });

    it('links the content type on the dashboard', () => {
        const wrapper = mount(Meta, { propsData: { type: 'dashboard', size: 'normal', record } });

        const link = wrapper.find('.listing__row--list a');
        expect(link.attributes('href')).toBe('/bolt/content/pages');
        expect(link.text()).toBe('Page');
    });

    it('renders the content type as plain text outside the dashboard', () => {
        const wrapper = mount(Meta, { propsData: { type: 'pages', size: 'normal', record } });

        expect(wrapper.find('.listing__row--list a').exists()).toBe(false);
        expect(wrapper.text()).toContain('Page');
    });
});
