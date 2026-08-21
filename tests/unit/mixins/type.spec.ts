import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import type from '@/listing/mixins/type';
import { createListingStore } from '../helpers/store';

/** The listing content-type mixin, used by Filter.vue and the table rows. */
const host = {
    name: 'TypeMixinHost',
    mixins: [type],
    template: '<div class="type">{{ type }}</div>',
};

describe('listing type mixin', () => {
    it('exposes the content type from the store', () => {
        const { store, localVue } = createListingStore({ general: { type: 'pages' } });

        const wrapper = mount(host, { localVue, store });

        expect(wrapper.find('.type').text()).toBe('pages');
    });

    it('renders nothing before a type is set', () => {
        const { store, localVue } = createListingStore();

        const wrapper = mount(host, { localVue, store });

        expect(wrapper.find('.type').text()).toBe('');
    });

    it('follows a change of type', async () => {
        const { store, localVue } = createListingStore({ general: { type: 'pages' } });
        const wrapper = mount(host, { localVue, store });

        await store.dispatch('general/setType', 'dashboard');
        await nextTick();

        expect(wrapper.find('.type').text()).toBe('dashboard');
    });
});
