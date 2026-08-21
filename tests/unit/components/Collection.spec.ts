import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { afterEach, describe, expect, it } from 'vitest';
import $ from 'jquery';
import { flushPromises } from '../helpers/async';
import Collection from '@/editor/Components/Collection.vue';

/**
 * Collection.vue is driven by jQuery rather than Vue: the collection items come
 * from Twig, and mounted() delegates click handlers off `document` for them. So
 * this spec mounts into the document and drives real jQuery events.
 *
 * Those delegated handlers are never torn down — the component has no
 * beforeDestroy — so every mount adds another set to `document`. Left alone
 * they accumulate across tests and a single click fires the same handler once
 * per mount, which quietly cancels out the toggles. The afterEach below removes
 * them so each test starts clean.
 *
 * Worth knowing: nothing removes them in the browser either. It does not bite
 * today only because a collection field mounts once per page load.
 */

const labels = {
    field_label: 'Blocks',
    expand_all: 'Expand all',
    collapse_all: 'Collapse all',
    add_collection_item: 'Add block',
    select: 'Select a block',
};

// The buttons Twig renders into each item. The item title is rendered by the
// Vue template itself, not here.
const itemMarkup = `
    <div class="d-flex align-items-center">
        <button class="action-move-up-collection-item"></button>
        <button class="action-move-down-collection-item"></button>
        <button class="action-remove-collection-item"></button>
    </div>
`;

const field = (hash: string, title: string) => ({
    hash,
    label: 'Text block',
    icon: 'fa-font',
    buttons: `<div>${itemMarkup}</div>`,
    content: `<div><input type="text" name="fields[blocks][${hash}]" value="${title}"></div>`,
});

const templates = [
    {
        label: 'Text block',
        icon: 'fa-font',
        hash: 'TEMPLATEHASH',
        buttons: `<div>${itemMarkup}</div>`,
        content: '<div><input type="text" name="fields[blocks][TEMPLATEHASH]" value=""></div>',
    },
    {
        label: 'Image block',
        icon: 'fa-image',
        hash: 'IMAGEHASH',
        buttons: `<div>${itemMarkup}</div>`,
        content: '<div><input type="text" name="fields[blocks][IMAGEHASH]" value=""></div>',
    },
];

const defaultProps = {
    name: 'blocks',
    templates,
    existingFields: [] as unknown[],
    labels,
    limit: 5,
    variant: 'collapsed',
};

const mountCollection = (props: Record<string, unknown> = {}) =>
    mount(Collection, {
        propsData: { ...defaultProps, existingFields: [], ...props },
        attachTo: document.body,
    });

const items = (wrapper: ReturnType<typeof mount>) => wrapper.findAll('.collection-item').wrappers;
const titles = (wrapper: ReturnType<typeof mount>) =>
    items(wrapper).map(item => item.find('.collection-item-title').text());
const addButton = (wrapper: ReturnType<typeof mount>) => wrapper.find('button.btn-small, a.dropdown-item');

afterEach(() => {
    $(document).off('click');
    $('#modalButtonAccept').off('click');
});

describe('EditorCollection', () => {
    it('renders the field label and the expand controls', () => {
        const wrapper = mountCollection();

        expect(wrapper.find('label').text()).toBe('Blocks:');
        expect(wrapper.find('.collection-expand-all').text()).toBe('Expand all');
        expect(wrapper.find('.collection-collapse-all').text()).toBe('Collapse all');
        expect(wrapper.attributes('id')).toBe('blocks');
    });

    it('renders an item per existing field', async () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'First'), field('b', 'Second')] });
        // The titles are filled in from each item's first text field by a
        // jQuery ready handler, so they land a turn after mount.
        await flushPromises();

        expect(items(wrapper)).toHaveLength(2);
        expect(titles(wrapper)).toEqual(['First', 'Second']);
    });

    it('titles a new item with its block label until a field is filled in', async () => {
        const wrapper = mountCollection();

        await addButton(wrapper).trigger('click');

        expect(titles(wrapper)).toEqual(['Text block']);
    });

    it('compiles the Twig-rendered content of each item', () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'First')] });

        expect(wrapper.find('input[name="fields[blocks][a]"]').exists()).toBe(true);
    });

    it('starts items collapsed unless the field is expanded', () => {
        const collapsed = mountCollection({ existingFields: [field('a', 'First')], variant: 'collapsed' });
        expect(items(collapsed)[0].classes()).toContain('collapsed');

        const expanded = mountCollection({ existingFields: [field('a', 'First')], variant: 'expanded' });
        expect(items(expanded)[0].classes()).not.toContain('collapsed');
    });

    it('offers a dropdown when there are several templates', () => {
        const wrapper = mountCollection();

        expect(wrapper.find('.dropdown').exists()).toBe(true);
        const options = wrapper.findAll('a.dropdown-item').wrappers;
        expect(options.map(option => option.attributes('data-template'))).toEqual(['Text block', 'Image block']);
    });

    it('offers a single button when there is only one template', () => {
        const wrapper = mountCollection({ templates: [templates[0]] });

        expect(wrapper.find('.dropdown').exists()).toBe(false);
        expect(wrapper.find('button.btn-small').attributes('data-template')).toBe('Text block');
        expect(wrapper.find('button.btn-small').text()).toBe('Add block');
    });

    it('adds an item of the chosen template', async () => {
        const wrapper = mountCollection();

        await wrapper.findAll('a.dropdown-item').at(1).trigger('click');

        expect(items(wrapper)).toHaveLength(1);
        expect(titles(wrapper)).toEqual(['Image block']);
    });

    it('gives each added item its own hash so the fields do not collide', async () => {
        const wrapper = mountCollection();

        await addButton(wrapper).trigger('click');
        await addButton(wrapper).trigger('click');

        const names = wrapper.findAll('.collection-item input').wrappers.map(input => input.attributes('name'));
        expect(names).toHaveLength(2);
        expect(names[0]).not.toBe(names[1]);
        expect(names[0]).not.toContain('TEMPLATEHASH');
    });

    it('leaves the template itself untouched when adding', async () => {
        const wrapper = mountCollection();

        await addButton(wrapper).trigger('click');

        expect(templates[0].hash).toBe('TEMPLATEHASH');
        expect(templates[0].content).toContain('TEMPLATEHASH');
    });

    it('stops allowing more once the limit is reached', async () => {
        const wrapper = mountCollection({ limit: 1 });
        expect(wrapper.find('button.dropdown-toggle').attributes('disabled')).toBeUndefined();

        await addButton(wrapper).trigger('click');

        expect(wrapper.find('button.dropdown-toggle').attributes('disabled')).toBeDefined();
    });

    it('counts existing fields against the limit', () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'First')], limit: 1 });

        expect(wrapper.find('button.dropdown-toggle').attributes('disabled')).toBeDefined();
    });

    it('expands and collapses every item at once', async () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'First'), field('b', 'Second')] });

        wrapper.find('.collection-expand-all').element.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        await nextTick();
        expect(items(wrapper).every(item => !item.classes().includes('collapsed'))).toBe(true);

        wrapper.find('.collection-collapse-all').element.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        await nextTick();
        expect(items(wrapper).every(item => item.classes().includes('collapsed'))).toBe(true);
    });

    it('cannot move the first item up or the last one down', () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'First'), field('b', 'Second')] });

        const [first, last] = items(wrapper);
        expect(first.find('.action-move-up-collection-item').attributes('disabled')).toBe('disabled');
        expect(first.find('.action-move-down-collection-item').attributes('disabled')).toBeUndefined();
        expect(last.find('.action-move-down-collection-item').attributes('disabled')).toBe('disabled');
        expect(last.find('.action-move-up-collection-item').attributes('disabled')).toBeUndefined();
    });

    it('moves an item up, changing the submitted order', async () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'First'), field('b', 'Second')] });

        const secondItem = items(wrapper)[1];
        secondItem
            .find('.action-move-up-collection-item')
            .element.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        await nextTick();

        const order = $('#blocks .collection-item input')
            .toArray()
            .map(input => (input as HTMLInputElement).name);
        expect(order).toEqual(['fields[blocks][b]', 'fields[blocks][a]']);
    });

    it('moves an item down, changing the submitted order', async () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'First'), field('b', 'Second')] });

        items(wrapper)[0]
            .find('.action-move-down-collection-item')
            .element.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        await nextTick();

        const order = $('#blocks .collection-item input')
            .toArray()
            .map(input => (input as HTMLInputElement).name);
        expect(order).toEqual(['fields[blocks][b]', 'fields[blocks][a]']);
    });

    it('collapses an item when its summary is clicked', async () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'First')], variant: 'expanded' });

        wrapper.find('.summary').element.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        await nextTick();

        expect(items(wrapper)[0].classes()).toContain('collapsed');
    });

    it('removes an item once the confirmation is accepted', async () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'First'), field('b', 'Second')] });
        document.body.insertAdjacentHTML('beforeend', '<button id="modalButtonAccept"></button>');

        items(wrapper)[0]
            .find('.action-remove-collection-item')
            .element.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        $('#modalButtonAccept').trigger('click');
        await nextTick();

        const remaining = $('#blocks .collection-item input')
            .toArray()
            .map(input => (input as HTMLInputElement).name);
        expect(remaining).toEqual(['fields[blocks][b]']);
    });

    it('frees up a slot again after a removal', async () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'First')], limit: 1 });
        document.body.insertAdjacentHTML('beforeend', '<button id="modalButtonAccept"></button>');
        expect(wrapper.find('button.dropdown-toggle').attributes('disabled')).toBeDefined();

        items(wrapper)[0]
            .find('.action-remove-collection-item')
            .element.dispatchEvent(new MouseEvent('click', { bubbles: true }));
        await nextTick();

        expect(wrapper.find('button.dropdown-toggle').attributes('disabled')).toBeUndefined();
    });

    it('shows the value of the first text field as the item title', async () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'Original title')] });
        await flushPromises();

        const input = wrapper.find('.collection-item input').element as HTMLInputElement;
        input.value = 'Edited title';
        input.dispatchEvent(new Event('keyup', { bubbles: true }));

        expect(wrapper.find('.collection-item-title').text()).toBe('Edited title');
    });

    it('falls back to the block label when the field is emptied', async () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'Original title')] });
        await flushPromises();

        const input = wrapper.find('.collection-item input').element as HTMLInputElement;
        input.value = '';
        input.dispatchEvent(new Event('keyup', { bubbles: true }));

        expect(wrapper.find('.collection-item-title').text()).toBe('Text block');
    });

    it('renders a title containing markup as plain text', async () => {
        const wrapper = mountCollection({ existingFields: [field('a', 'Original title')] });
        await flushPromises();

        const input = wrapper.find('.collection-item input').element as HTMLInputElement;
        input.value = '<img src=x onerror=alert(1)>Bad';
        input.dispatchEvent(new Event('keyup', { bubbles: true }));

        const title = wrapper.find('.collection-item-title');
        expect(title.text()).toBe('Bad');
        expect(title.find('img').exists()).toBe(false);
    });
});
