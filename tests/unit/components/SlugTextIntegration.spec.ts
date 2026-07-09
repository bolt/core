import { mount } from '@vue/test-utils';
import { describe, expect, it, beforeEach, afterEach, vi } from 'vitest';
import Slug from '@/editor/Components/Slug.vue';
import Text from '@/editor/Components/Text.vue';
import { eventBus } from '@/eventBus';

/**
 * Integration guards for the Slug <-> Text auto-generation flow, which is wired
 * through the shared `eventBus` (mitt) rather than Vuex/$root as it was in Vue 2.
 *
 * These lock in the behaviour investigated for two review findings:
 *  - the supported top-level "title drives slug" flow must keep working;
 *  - a field nested inside a collection (e.g. `fields[blocks][hash][title]`)
 *    must NOT drive the page's top-level slug (no cross-talk). This is why the
 *    Text field keys itself on the FIRST bracketed segment of its `name`
 *    (`fields[<key>]`), so nested names resolve to their outer container key
 *    ('blocks') and never collide with a top-level slug's source ('title').
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
    errormessage: false as const,
    pattern: false as const,
    localize: false,
    isNew: true,
};

const textProps = {
    value: '',
    name: 'fields[title]',
    type: 'text',
    disabled: false,
    id: 'field-title',
    required: false,
    readonly: false,
    errormessage: false as const,
    pattern: false as const,
    placeholder: false as const,
    autofocus: false,
};

describe('Slug/Text auto-generation integration', () => {
    beforeEach(() => {
        vi.useFakeTimers();
        eventBus.all.clear();
        document.body.innerHTML = '';
    });

    afterEach(() => {
        vi.runAllTimers();
        document.body.innerHTML = '';
        vi.useRealTimers();
        vi.restoreAllMocks();
    });

    it('regenerates a top-level slug end-to-end when the title field is typed into', async () => {
        // Mount Text first so its generate-from-title listener exists before the
        // Slug's mounted setTimeout(0) broadcasts that generation is active.
        const text = mount(Text, { props: textProps, attachTo: document.body });
        const slug = mount(Slug, { props: slugProps, attachTo: document.body });

        vi.runAllTimers(); // fire Slug's mounted setTimeout(0)
        await slug.vm.$nextTick();

        const input = text.find('input');
        (input.element as HTMLInputElement).value = 'Hello World';
        await input.trigger('input');
        await slug.vm.$nextTick();

        expect((slug.vm as unknown as { val: string }).val).toBe('hello-world');

        text.unmount();
        slug.unmount();
    });

    it('does not let a field nested in a collection drive the top-level slug', async () => {
        const emitSpy = vi.spyOn(eventBus, 'emit');

        const nested = mount(Text, {
            props: { ...textProps, name: 'fields[blocks][hash][title]' },
            attachTo: document.body,
        });

        // Simulate the top-level slug enabling generation for source "title".
        eventBus.emit('generate-from-title', { sources: ['title'], active: true });

        const input = nested.find('input');
        (input.element as HTMLInputElement).value = 'Nested Block Title';
        await input.trigger('input');

        // The nested field keys on 'blocks' (its outer container), so it must not
        // emit a slugify-from-title that the top-level slug (source 'title') acts on.
        const slugifyEmits = emitSpy.mock.calls.filter(([event]) => event === 'slugify-from-title');
        expect(slugifyEmits).toHaveLength(0);

        nested.unmount();
    });
});
