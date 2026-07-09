import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import { describe, expect, it, beforeEach, afterEach } from 'vitest';
import $ from 'jquery';
import Collection from '@/editor/Components/Collection.vue';

const labels = {
    add_collection_item: 'Add collection item',
    collapse_all: 'Collapse all',
    expand_all: 'Expand all',
    field_label: 'Collection',
    select: 'Select',
};

const getCollectionHashOrder = (collection: JQuery<HTMLElement>) =>
    collection
        .children('.collection-item')
        .map((_, el) => $(el).attr('data-collection-hash'))
        .get();

describe('EditorCollection', () => {
    type CollectionExpose = {
        elements: Array<{ label: string; icon: string; hash: string; buttons: string; content: string }>;
        compile: (element: string) => { template: string } | undefined;
    };
    let wrapper: VueWrapper<ComponentPublicInstance & CollectionExpose>;

    const templates = [
        {
            label: 'Text',
            icon: 'fa-font',
            hash: 'templatehash',
            buttons:
                '<button type="button" class="action-move-up-collection-item">Up</button>' +
                '<button type="button" class="action-move-down-collection-item">Down</button>' +
                '<button type="button" class="action-remove-collection-item">Remove</button>',
            content: '<input name="fields[blocks][templatehash][text]" type="text" value="" />',
        },
        {
            label: 'Image',
            icon: 'fa-image',
            hash: 'templatehash2',
            buttons: '<button type="button">Move</button>',
            content: '<input name="fields[blocks][templatehash2][image]" type="text" value="" />',
        },
    ];

    beforeEach(() => {
        (window as Window & typeof globalThis & { $: typeof $ }).$ = $;
        $.fn.ready = function (this: JQuery<HTMLElement>, fn: ($: JQueryStatic) => void) {
            fn($);
            return this;
        };
        // Mock modal behavior
        document.body.innerHTML = `
            <div id="modalButtonAccept"></div>
            <div id="editor"></div>
        `;
    });

    afterEach(() => {
        wrapper.unmount();
        document.body.innerHTML = '';
        $(document).off(); // clear jquery events
    });

    it('renders with existing fields and allows interactions', async () => {
        wrapper = mount(Collection, {
            props: {
                name: 'fields_blocks',
                templates,
                existingFields: [
                    {
                        label: 'Text',
                        icon: 'fa-font',
                        hash: 'hash1',
                        buttons: templates[0].buttons,
                        content: '<input name="fields[blocks][hash1][text]" type="text" value="hello" />',
                    },
                    {
                        label: 'Image',
                        icon: 'fa-image',
                        hash: 'hash2',
                        buttons: templates[1].buttons,
                        content: '<textarea name="fields[blocks][hash2][image]">world</textarea>',
                    },
                ],
                labels,
                limit: 10,
                variant: 'expanded',
            },
            attachTo: document.body,
        });

        // Fix coverage

        // Compile cache
        const compiled = wrapper.vm.compile('test');
        const compiled2 = wrapper.vm.compile('test');
        expect(compiled).toBe(compiled2);

        // Wait for DOM
        await wrapper.vm.$nextTick();

        const collection = $('#fields_blocks');

        // Test expand/collapse all
        collection.find('.collection-collapse-all').trigger('click');
        expect(collection.find('.collection-item').first().hasClass('collapsed')).toBe(true);

        collection.find('.collection-expand-all').trigger('click');
        expect(collection.find('.collection-item').first().hasClass('collapsed')).toBe(false);

        // Test item summary click (toggle collapsed)
        collection.find('.collection-item .summary').first().trigger('click');
        expect(collection.find('.collection-item').first().hasClass('collapsed')).toBe(true);

        // Test title update via keyup and change natively via Vue wrappers (to trigger the delegated jQuery event)
        const firstInput = wrapper.find('input[type="text"]');
        (firstInput.element as HTMLInputElement).value = 'new text';
        firstInput.trigger('keyup');
        await wrapper.vm.$nextTick();

        const secondTextarea = wrapper.find('textarea');
        (secondTextarea.element as HTMLTextAreaElement).value = 'other text';
        secondTextarea.trigger('change');
        await wrapper.vm.$nextTick();

        // Also trigger directly via jQuery to guarantee execution
        collection.find('.collection-item').first().trigger('keyup');
        collection.find('.collection-item').last().trigger('change');

        // Test Move Down
        collection.find('.collection-item').first().find('.action-move-down-collection-item').trigger('click');

        // Test Move Up
        collection.find('.collection-item').last().find('.action-move-up-collection-item').trigger('click');

        // Test Remove
        collection.find('.collection-item').first().find('.action-remove-collection-item').trigger('click');
        $('#modalButtonAccept').trigger('click');

        // Add item using click
        await wrapper.find('.dropdown-item').trigger('click');
        expect(wrapper.vm.counter).toBe(2);
    });

    it('preserves the manually reordered submission order after a subsequent add', async () => {
        const orderedFields = ['A', 'B', 'C'].map(label => ({
            label,
            icon: 'fa-font',
            hash: `hash${label}`,
            buttons: templates[0].buttons,
            content: `<input name="fields[blocks][hash${label}][text]" value="${label}" />`,
        }));

        wrapper = mount(Collection, {
            props: {
                name: 'fields_blocks_reorder_add',
                templates,
                existingFields: orderedFields,
                labels,
                limit: 10,
                variant: 'expanded',
            },
            attachTo: document.body,
        });
        await wrapper.vm.$nextTick();

        const collection = $('#fields_blocks_reorder_add');
        const domOrder = () => getCollectionHashOrder(collection);

        expect(domOrder()).toEqual(['hashA', 'hashB', 'hashC']);

        // Move the last item (C) up -> [A, C, B]
        collection.children('.collection-item').last().find('.action-move-up-collection-item').trigger('click');
        expect(domOrder()).toEqual(['hashA', 'hashC', 'hashB']);

        // Add a new item. The DOM (== what gets submitted, via hash-named inputs)
        // must keep the manual reorder rather than snapping back to [A, B, C].
        await wrapper.find('button[data-template="Text"]').trigger('click');
        await wrapper.vm.$nextTick();

        expect(domOrder().slice(0, 3)).toEqual(['hashA', 'hashC', 'hashB']);
        expect(domOrder()).toHaveLength(4);
    });

    it('preserves the manual reorder after removing a different item', async () => {
        const orderedFields = ['A', 'B', 'C'].map(label => ({
            label,
            icon: 'fa-font',
            hash: `hash${label}`,
            buttons: templates[0].buttons,
            content: `<input name="fields[blocks][hash${label}][text]" value="${label}" />`,
        }));

        wrapper = mount(Collection, {
            props: {
                name: 'fields_blocks_reorder_remove',
                templates,
                existingFields: orderedFields,
                labels,
                limit: 10,
                variant: 'expanded',
            },
            attachTo: document.body,
        });
        await wrapper.vm.$nextTick();

        const collection = $('#fields_blocks_reorder_remove');
        const domOrder = () => getCollectionHashOrder(collection);

        // Move C up -> [A, C, B]
        collection.children('.collection-item').last().find('.action-move-up-collection-item').trigger('click');
        expect(domOrder()).toEqual(['hashA', 'hashC', 'hashB']);

        // Remove the first item (A); the moved order of the rest must survive.
        collection.children('.collection-item').first().find('.action-remove-collection-item').trigger('click');
        $('#modalButtonAccept').trigger('click');
        await wrapper.vm.$nextTick();

        expect(domOrder()).toEqual(['hashC', 'hashB']);
    });

    it('adds an item when there is only one template', () => {
        wrapper = mount(Collection, {
            props: {
                name: 'fields_blocks_single',
                templates: [templates[0]],
                existingFields: [],
                labels,
                limit: 1,
                variant: 'collapsed',
            },
            attachTo: document.body,
        });

        const btn = wrapper.find('button[data-template="Text"]');
        btn.trigger('click');

        expect(wrapper.vm.counter).toBe(1);
        expect(wrapper.vm.allowMore).toBe(false);
    });

    it('does not decrement the item counter until removal is confirmed', async () => {
        wrapper = mount(Collection, {
            props: {
                name: 'fields_blocks_confirmed_remove',
                templates,
                existingFields: [
                    {
                        label: 'Text',
                        icon: 'fa-font',
                        hash: 'hash1',
                        buttons: templates[0].buttons,
                        content: '<input name="fields[blocks][hash1][text]" type="text" value="hello" />',
                    },
                ],
                labels,
                limit: 1,
                variant: 'expanded',
            },
            attachTo: document.body,
        });

        await wrapper.vm.$nextTick();

        const collection = $('#fields_blocks_confirmed_remove');
        collection.find('.action-remove-collection-item').trigger('click');

        expect(wrapper.vm.counter).toBe(1);
        expect(wrapper.vm.allowMore).toBe(false);
        expect(collection.find('.collection-item')).toHaveLength(1);

        $('#modalButtonAccept').trigger('click');
        await wrapper.vm.$nextTick();

        expect(wrapper.vm.counter).toBe(0);
        expect(wrapper.vm.elements).toHaveLength(0);
        expect(wrapper.vm.allowMore).toBe(true);
        expect(collection.find('.collection-item')).toHaveLength(0);

        await wrapper.vm.$forceUpdate();
        await wrapper.vm.$nextTick();

        expect(collection.find('.collection-item')).toHaveLength(0);
    });
});
