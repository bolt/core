import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Slug from '@/editor/Components/Slug.vue';
import Text from '@/editor/Components/Text.vue';
import { flushPromises } from '../helpers/async';

/**
 * The Slug ⇄ Text auto-generation flow end to end, over the real event bus
 * rather than through helpers/bus.ts.
 *
 * Both are attached to the document: Slug reads its source fields straight off
 * it, by the same `fields[...]` name the Text field renders.
 */

const slugProps = {
    value: '',
    name: 'fields[slug]',
    generate: 'title',
    labels: {
        button_edit: 'Edit',
        button_locked: 'Locked',
        button_unlocked: 'Unlocked',
        generate_from: 'Generate from',
    },
    required: false,
    readonly: false,
    errormessage: false,
    pattern: false,
    localize: false,
    isNew: true,
};

const mountText = (name: string, value = '') => mount(Text, { propsData: { value, name }, attachTo: document.body });

const mountSlug = (props: Record<string, unknown> = {}) =>
    mount(Slug, { propsData: { ...slugProps, ...props }, attachTo: document.body });

const mountPair = (slug: Record<string, unknown> = {}) => ({
    text: mountText('fields[title]'),
    slug: mountSlug(slug),
});

const slugValue = (slug: ReturnType<typeof mount>) => (slug.find('input').element as HTMLInputElement).value;

const clickItem = (slug: ReturnType<typeof mount>, label: string) => {
    const item = slug.findAll('.dropdown-item').wrappers.find(entry => entry.text().trim() === label);
    if (!item) {
        throw new Error(`No "${label}" action`);
    }
    return item.trigger('click');
};

// Slug's mounted() defers its work into a setTimeout.
const settle = async () => {
    await flushPromises();
    await nextTick();
};

describe('Slug and Text together', () => {
    it('regenerates a new record’s slug as its title is typed', async () => {
        const { text, slug } = mountPair();
        await settle();

        await text.find('input').setValue('Hello World');

        expect(slugValue(slug)).toBe('hello-world');
    });

    it('joins every source field into the slug', async () => {
        mountText('fields[title]', 'Hello');
        const subtitle = mountText('fields[subtitle]');
        const slug = mountSlug({ generate: 'title,subtitle' });
        await settle();

        await subtitle.find('input').setValue('World');

        expect(slugValue(slug)).toBe('hello-world');
    });

    it('leaves an existing record’s slug alone when its title is typed', async () => {
        const { text, slug } = mountPair({ value: 'existing-slug', isNew: false });
        await settle();

        await text.find('input').setValue('Hello World');

        expect(slugValue(slug)).toBe('existing-slug');
    });

    it('follows the title once an existing record’s slug is generated from it', async () => {
        const { text, slug } = mountPair({ value: 'existing-slug', isNew: false });
        await settle();

        await clickItem(slug, 'Generate from title');
        await text.find('input').setValue('Hello World');

        expect(slugValue(slug)).toBe('hello-world');
    });

    it.each(['Edit', 'Locked'])('stops following the title after "%s"', async action => {
        const { text, slug } = mountPair();
        await settle();
        await text.find('input').setValue('Hello');

        await clickItem(slug, action);
        await text.find('input').setValue('Hello World');

        expect(slugValue(slug)).toBe('hello');
    });

    it('follows the title again once a slug edited by hand is generated from it', async () => {
        const { text, slug } = mountPair();
        await settle();

        await clickItem(slug, 'Edit');
        await clickItem(slug, 'Generate from title');
        await text.find('input').setValue('Hello World');

        expect(slugValue(slug)).toBe('hello-world');
    });

    it('follows the title of a localised record whose title is still empty', async () => {
        const { text, slug } = mountPair({ localize: true, isNew: false });
        await settle();

        await text.find('input').setValue('Hello World');

        expect(slugValue(slug)).toBe('hello-world');
    });

    it('still follows and stops after a text field in a collection is typed in and removed', async () => {
        const nested = mountText('collections[collection][set][abc][title]');
        const { text, slug } = mountPair();
        await settle();

        await nested.find('input').setValue('Nested block title');
        expect(slugValue(slug)).toBe('');

        // As when the collection re-renders its items.
        nested.destroy();
        await text.find('input').setValue('Hello');
        expect(slugValue(slug)).toBe('hello');

        await clickItem(slug, 'Edit');
        await text.find('input').setValue('Hello World');

        expect(slugValue(slug)).toBe('hello');
    });
});
