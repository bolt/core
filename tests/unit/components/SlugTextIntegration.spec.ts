import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Slug from '@/editor/Components/Slug.vue';
import Text from '@/editor/Components/Text.vue';
import { recordBus } from '../helpers/bus';

/**
 * The Slug ⇄ Text auto-generation flow end to end, over the real event bus
 * rather than through helpers/bus.ts.
 *
 * A Text field keys itself on the *first* bracketed segment of its name
 * (`fields[<key>]`), so a field nested in a collection —
 * `fields[blocks][hash][title]` — resolves to its outer container, `blocks`,
 * and never drives the page's top-level `title` slug.
 */

const slugProps = {
    value: '',
    name: 'slugField',
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

const textProps = { value: '', name: 'fields[title]', id: 'field-title' };

// Slug's mounted hook defers its work into a setTimeout.
const settle = async () => {
    await new Promise(resolve => setTimeout(resolve, 0));
    await nextTick();
};

describe('Slug and Text together', () => {
    it('regenerates a new record’s slug as its title is typed', async () => {
        // Attached: Slug reads the title straight off the document. Text goes
        // first so it is listening when Slug announces that generation is on.
        const text = mount(Text, { props: textProps, attachTo: document.body });
        const slug = mount(Slug, { props: slugProps, attachTo: document.body });
        await settle();

        await text.find('input').setValue('Hello World');

        expect((slug.find('input').element as HTMLInputElement).value).toBe('hello-world');
    });

    it('does not let a field nested in a collection drive the top-level slug', async () => {
        const nested = mount(Text, {
            props: { ...textProps, name: 'fields[blocks][hash][title]' },
            attachTo: document.body,
        });
        const asked = recordBus('slugify-from-title');
        // Switches generation on for its `title` source, as on page load.
        mount(Slug, { props: slugProps, attachTo: document.body });
        await settle();

        await nested.find('input').setValue('Nested block title');

        expect(asked).toEqual([]);
    });
});
