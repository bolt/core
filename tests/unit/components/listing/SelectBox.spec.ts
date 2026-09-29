import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import SelectBox from '@/listing/Components/SelectBox.vue';
import { createListingStore, type ListingSeed } from '../../helpers/store';
import { multiselectStub } from '../../helpers/stubs';

const labels = {
    selected: 'selected',
    update_all: 'Update all',
    status_to_published: 'Publish',
    status_to_draft: 'Make draft',
    status_to_held: 'Hold',
    delete: 'Delete',
};

const propsData = {
    singular: 'record',
    plural: 'records',
    labels,
    csrftoken: 'token-123',
    backendPrefix: '/bolt/',
};

const mountSelectBox = (seed: ListingSeed, props: Record<string, unknown> = {}) => {
    const testStore = createListingStore(seed);
    return mount(SelectBox, {
        localVue: testStore.localVue,
        store: testStore.store,
        propsData: { ...propsData, ...props },
        stubs: { multiselect: multiselectStub },
    });
};

const chooseAction = async (wrapper: ReturnType<typeof mount>, key: string) => {
    const multiselect = wrapper.findComponent(multiselectStub);
    const option = (multiselect.props('options') as { key: string }[]).find(item => item.key === key);
    (multiselect.vm as unknown as { choose: (option: unknown) => void }).choose(option);
    await nextTick();
};

describe('Listing SelectBox', () => {
    it('stays hidden while nothing is selected', () => {
        const wrapper = mountSelectBox({ selecting: { selectedCount: 0 } });

        expect(wrapper.find('.card').exists()).toBe(false);
    });

    it('uses the singular noun for a single selected record', () => {
        const wrapper = mountSelectBox({ selecting: { selectedCount: 1, selected: [1] } });

        // The count and the noun are separated visually by a css margin, not
        // by whitespace, so the text nodes run together.
        expect(wrapper.find('.card-header .is-primary').text()).toBe('1');
        expect(wrapper.find('.card-header').text()).toContain('record selected');
        expect(wrapper.find('.card-header').text()).not.toContain('records selected');
    });

    it('uses the plural noun for several selected records', () => {
        const wrapper = mountSelectBox({ selecting: { selectedCount: 3, selected: [1, 2, 3] } });

        expect(wrapper.find('.card-header .is-primary').text()).toBe('3');
        expect(wrapper.find('.card-header').text()).toContain('records selected');
    });

    it('offers the three status changes and delete', () => {
        const wrapper = mountSelectBox({ selecting: { selectedCount: 1, selected: [1] } });

        const options = wrapper.findComponent(multiselectStub).props('options') as { key: string; value: string }[];
        expect(options.map(option => option.key)).toEqual([
            'status/published',
            'status/draft',
            'status/held',
            'delete',
        ]);
        expect(options.map(option => option.value)).toEqual(['Publish', 'Make draft', 'Hold', 'Delete']);
    });

    it('renders an icon and a label for each option', () => {
        const wrapper = mountSelectBox({ selecting: { selectedCount: 1, selected: [1] } });

        const first = wrapper.findAll('.stub-option').at(0);
        expect(first.find('span').classes()).toContain('is-published');
        expect(first.text()).toBe('Publish');
    });

    it('disables the submit button until an action is chosen', async () => {
        const wrapper = mountSelectBox({ selecting: { selectedCount: 1, selected: [1] } });

        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeDefined();

        await chooseAction(wrapper, 'status/published');

        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeUndefined();
    });

    it('posts to the bulk endpoint for the chosen action', async () => {
        const wrapper = mountSelectBox({ selecting: { selectedCount: 2, selected: [1, 2] } });

        expect(wrapper.find('form').attributes('action')).toBe('');

        await chooseAction(wrapper, 'delete');

        expect(wrapper.find('form').attributes('action')).toBe('/bolt/bulk/delete');
    });

    it('builds a relative post url when there is no backend prefix', async () => {
        const wrapper = mountSelectBox({ selecting: { selectedCount: 1, selected: [1] } }, { backendPrefix: '' });

        await chooseAction(wrapper, 'status/held');

        expect(wrapper.find('form').attributes('action')).toBe('bulk/status/held');
    });

    it('submits the selected ids and the csrf token', () => {
        const wrapper = mountSelectBox({ selecting: { selectedCount: 2, selected: [4, 9] } });

        expect(wrapper.find('input[name="records"]').attributes('value')).toBe('4,9');
        expect(wrapper.find('input[name="_csrf_token"]').attributes('value')).toBe('token-123');
    });

    it('exposes the record order, though nothing submits it', () => {
        // The `order` computed is dead code — no template or form field reads
        // it. Pinned through the instance so the Vue 3 migration can see it.
        const testStore = createListingStore({
            selecting: { selectedCount: 1, selected: [1] },
            listing: { records: [{ id: 3 }, { id: 1 }] },
        });
        const wrapper = mount(SelectBox, {
            localVue: testStore.localVue,
            store: testStore.store,
            propsData,
            stubs: { multiselect: multiselectStub },
        });

        expect((wrapper.vm as unknown as { order: number[] }).order).toEqual([3, 1]);
        expect(wrapper.find('form').html()).not.toContain('3,1');
    });

    it('labels the submit button', () => {
        const wrapper = mountSelectBox({ selecting: { selectedCount: 1, selected: [1] } });

        expect(wrapper.find('button[type="submit"]').text()).toBe('Update all');
    });
});
