import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it, vi } from 'vitest';
import Slug from '@/editor/Components/Slug.vue';
import { withTitleFields } from '../helpers/dom';
import { flushPromises } from '../helpers/async';
import { emitOnBus, recordBus } from '../helpers/bus';

const labels = {
    button_locked: 'Locked',
    button_unlocked: 'Unlocked',
    button_edit: 'Edit',
    generate_from: 'Generate from',
};

const defaultProps = {
    value: 'existing-slug',
    name: 'fields[slug]',
    prefix: 'https://example.org/',
    fieldClass: 'is-slug',
    generate: 'title',
    labels,
    required: true,
    readonly: false,
    errormessage: 'Required',
    pattern: '[a-z-]+',
    localize: false,
    isNew: false,
};

const mountSlug = (props: Record<string, unknown> = {}) => mount(Slug, { propsData: { ...defaultProps, ...props } });

const input = (wrapper: ReturnType<typeof mount>) => wrapper.find('input').element as HTMLInputElement;
const toggleButton = (wrapper: ReturnType<typeof mount>) => wrapper.find('button.dropdown-toggle');
const dropdownItems = (wrapper: ReturnType<typeof mount>) =>
    wrapper.findAll('.dropdown-item').wrappers.map(item => item.text().trim());

// Which actions are offered depends on the current state, so pick by label
// rather than by position.
const clickItem = (wrapper: ReturnType<typeof mount>, label: string) => {
    const item = wrapper.findAll('.dropdown-item').wrappers.find(entry => entry.text().trim() === label);
    if (!item) {
        throw new Error(`No "${label}" action; offered: ${dropdownItems(wrapper).join(', ')}`);
    }
    return item.trigger('click');
};

// mounted() defers its work into a setTimeout, so the initial state settles a
// macrotask later.
const settle = async () => {
    await flushPromises();
    await nextTick();
};

describe('EditorSlug', () => {
    it('renders the prefix and the current slug', async () => {
        withTitleFields({ title: 'A page' });
        const wrapper = mountSlug();
        await settle();

        expect(wrapper.find('.input-group-text').text()).toBe('https://example.org/');
        expect(input(wrapper).value).toBe('existing-slug');
        expect(wrapper.find('input').attributes('name')).toBe('fields[slug]');
        expect(wrapper.find('input').attributes('pattern')).toBe('[a-z-]+');
        expect(wrapper.find('input').attributes('data-errormessage')).toBe('Required');
    });

    it('starts locked and read-only for an existing record', async () => {
        withTitleFields({ title: 'A page' });
        const wrapper = mountSlug();
        await settle();

        expect(toggleButton(wrapper).text()).toBe('Locked');
        expect(wrapper.find('button.dropdown-toggle i').classes()).toContain('fa-lock');
        expect(wrapper.find('input').attributes('readonly')).toBeDefined();
    });

    it('offers edit, lock and generate while locked', async () => {
        withTitleFields({ title: 'A page' });
        const wrapper = mountSlug();
        await settle();

        // The lock action only appears once the field has been unlocked.
        expect(dropdownItems(wrapper)).toEqual(['Edit', 'Generate from title']);
    });

    it('generates from the title on mount for a new record', async () => {
        withTitleFields({ title: 'A Brand New Page' });
        const wrapper = mountSlug({ isNew: true, value: '' });
        await settle();

        expect(input(wrapper).value).toBe('a-brand-new-page');
        expect(toggleButton(wrapper).text()).toBe('Unlocked');
        expect(wrapper.find('button.dropdown-toggle i').classes()).toContain('fa-unlock');
    });

    it('leaves an existing record alone on mount', async () => {
        withTitleFields({ title: 'A page' });
        const wrapper = mountSlug();
        await settle();

        expect(input(wrapper).value).toBe('existing-slug');
        expect(toggleButton(wrapper).text()).toBe('Locked');
    });

    it('generates on mount for a localised record whose title is still empty', async () => {
        withTitleFields({ title: '' });
        const wrapper = mountSlug({ localize: true, value: '' });
        await settle();

        expect(toggleButton(wrapper).text()).toBe('Unlocked');
    });

    it('leaves a localised record alone once its title is filled in', async () => {
        withTitleFields({ title: 'Already titled' });
        const wrapper = mountSlug({ localize: true });
        await settle();

        expect(input(wrapper).value).toBe('existing-slug');
        expect(toggleButton(wrapper).text()).toBe('Locked');
    });

    it('unlocks the field for manual editing', async () => {
        withTitleFields({ title: 'A page' });
        const wrapper = mountSlug();
        await settle();

        await clickItem(wrapper, 'Edit');

        expect(toggleButton(wrapper).text()).toBe('Edit');
        expect(wrapper.find('button.dropdown-toggle i').classes()).toContain('fa-pencil-alt');
        expect(wrapper.find('input').attributes('readonly')).toBeUndefined();
        // Editing releases the lock, so locking becomes available and the edit
        // action drops away.
        expect(dropdownItems(wrapper)).toEqual(['Locked', 'Generate from title']);
    });

    it('slugifies whatever was typed when locking again', async () => {
        withTitleFields({ title: 'A page' });
        const wrapper = mountSlug();
        await settle();

        await clickItem(wrapper, 'Edit');
        await wrapper.find('input').setValue('Some Messy Slug!');
        await clickItem(wrapper, 'Locked');

        expect(input(wrapper).value).toBe('some-messy-slug');
        expect(toggleButton(wrapper).text()).toBe('Locked');
    });

    it('regenerates from the title on demand', async () => {
        withTitleFields({ title: 'A Fresh Title' });
        const wrapper = mountSlug();
        await settle();

        await clickItem(wrapper, 'Generate from title');

        expect(input(wrapper).value).toBe('a-fresh-title');
        expect(toggleButton(wrapper).text()).toBe('Unlocked');
    });

    it('joins several source fields into one slug', async () => {
        withTitleFields({ heading: 'Hello', subtitle: 'World' });
        const wrapper = mountSlug({ generate: 'heading,subtitle' });
        await settle();

        await clickItem(wrapper, 'Generate from heading,subtitle');

        expect(input(wrapper).value).toBe('hello-world');
    });

    it('regenerates when a title field asks it to', async () => {
        withTitleFields({ title: 'A Fresh Title' });
        const wrapper = mountSlug();
        await settle();

        emitOnBus(wrapper, 'slugify-from-title');
        await nextTick();

        expect(input(wrapper).value).toBe('a-fresh-title');
    });

    it('tells the title fields to start driving the slug once it generates', async () => {
        withTitleFields({ title: 'A Fresh Title' });
        const wrapper = mountSlug();
        const generating = recordBus(wrapper, 'generate-from-title');
        await settle();

        await clickItem(wrapper, 'Generate from title');

        expect(generating).toEqual([[true]]);
    });

    it('tells the title fields to stop driving the slug once it is edited by hand', async () => {
        withTitleFields({ title: 'A page' });
        const wrapper = mountSlug();
        const generating = recordBus(wrapper, 'generate-from-title');
        await settle();

        await clickItem(wrapper, 'Edit');

        expect(generating).toEqual([[false]]);
    });

    it('disables the dropdown when the field is read-only', async () => {
        withTitleFields({ title: 'A page' });
        const wrapper = mountSlug({ readonly: true });
        await settle();

        expect(toggleButton(wrapper).attributes('disabled')).toBeDefined();
    });

    it('fails to initialise when a source field is missing from the page', async () => {
        // Pinning current behaviour: mounted() dereferences the result of
        // document.querySelector with no guard, so a `generate` naming a field
        // Twig did not render throws a TypeError. It happens inside a
        // setTimeout, so it escapes Vue's error handling entirely and surfaces
        // as an unhandled exception rather than being contained.
        vi.useFakeTimers();
        try {
            withTitleFields({ title: 'A page' });
            mountSlug({ generate: 'title,nonexistent' });

            expect(() => vi.runAllTimers()).toThrow(TypeError);
        } finally {
            vi.useRealTimers();
        }
    });

    it.skip('only responds to the title fields it is scoped to', () => {
        // Only passes on refactor/vue3-composition-api, where the mitt event bus
        // carries a `{ source }` payload. The Vue 2.7 $root bus broadcasts
        // 'slugify-from-title' with no payload, so every Slug on the page
        // regenerates whenever any Text field changes — see 3778f430.
    });
});
