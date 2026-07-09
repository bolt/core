import { mount } from '@vue/test-utils';
import Meta from '@/listing/Components/Table/Row/_Meta.vue';
import { describe, it, expect } from 'vitest';

describe('Meta Component', () => {
    const defaultProps = {
        type: 'articles',
        size: 'normal',
        record: {
            id: 42,
            status: 'published',
            authorName: 'John Doe',
            publishedAt: '2023-01-01T10:00:00Z',
            createdAt: '2022-01-01T10:00:00Z',
            extras: {
                icon: 'fa-file',
                singular_name: 'Article',
                contentTypeOverviewLink: '/overview/articles',
            },
        },
    };

    const mountComponent = (propsOverride = {}) => {
        return mount(Meta, {
            props: { ...defaultProps, ...propsOverride },
        });
    };

    it('renders published date and status correctly', () => {
        const wrapper = mountComponent();

        const firstLi = wrapper.find('li.text-nowrap');
        expect(firstLi.find('.status').classes()).toContain('is-published');
        expect(firstLi.text()).toContain('1/1/2023'); // Approximate output of date() filter
    });

    it('falls back to createdAt if publishedAt is null', () => {
        const wrapper = mountComponent({
            record: {
                ...defaultProps.record,
                publishedAt: null,
                createdAt: '2022-01-01T10:00:00Z',
            },
        });

        const firstLi = wrapper.find('li.text-nowrap');
        expect(firstLi.text()).toContain('1/1/2022');
    });

    it('renders author and content type correctly for normal size', () => {
        const wrapper = mountComponent();

        const lis = wrapper.findAll('li');
        expect(lis).toHaveLength(3);

        const authorLi = lis[1];
        expect(authorLi.text()).toContain('John Doe');

        const typeLi = lis[2];
        expect(typeLi.find('.fas').classes()).toContain('fa-file');
        expect(typeLi.text()).toContain('Article');
        expect(typeLi.text()).toContain('№ 42');
        expect(typeLi.find('a').exists()).toBe(false); // Because type != dashboard
    });

    it('renders content type link if type is dashboard', () => {
        const wrapper = mountComponent({ type: 'dashboard' });

        const lis = wrapper.findAll('li');
        const typeLi = lis[2];

        const link = typeLi.find('a');
        expect(link.exists()).toBe(true);
        expect(link.attributes('href')).toBe('/overview/articles');
        expect(link.text()).toBe('Article');
    });

    it('hides author and content type if size is not normal', () => {
        const wrapper = mountComponent({ size: 'small' });

        const lis = wrapper.findAll('li');
        expect(lis).toHaveLength(1); // Only the date should be visible
    });
});
