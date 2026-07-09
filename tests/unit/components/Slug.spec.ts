import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import Slug from '@/editor/Components/Slug.vue';
import { eventBus } from '@/eventBus';
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';

describe('EditorSlug Component', () => {
    type SlugExpose = {
        val: string | number | boolean | null | undefined;
        edit: boolean;
        locked: boolean;
        buttonText: string;
        icon: string;
        shouldGenerateFromTitle: (title: string) => boolean;
        editSlug: () => void;
        lockSlug: () => void;
        generateSlug: () => void;
    };
    let wrapper: VueWrapper<ComponentPublicInstance & SlugExpose> | null = null;

    const defaultProps = {
        value: 'initial-slug',
        name: 'slugField',
        prefix: '/page/',
        fieldClass: 'custom-class',
        generate: 'title,subtitle',
        labels: {
            button_edit: 'Edit',
            button_locked: 'Locked',
            button_unlocked: 'Unlocked',
            generate_from: 'Generate from',
        },
        required: true,
        readonly: false,
        errormessage: 'Error!',
        pattern: '[a-z0-9]+',
        localize: false,
        isNew: false,
    };
    const generatePayload = {
        sources: ['title', 'subtitle'],
        active: true,
    };
    const stopGeneratePayload = {
        sources: ['title', 'subtitle'],
        active: false,
    };

    beforeEach(() => {
        vi.useFakeTimers();
        // Set up the DOM for generateSlug functionality
        document.body.innerHTML = `
            <input name="fields[title]" value="Test Title" />
            <input name="fields[subtitle]" value="Subtitle!" />
        `;

        // Clear all mocks and event bus listeners
        vi.clearAllMocks();
        eventBus.all.clear();
    });

    afterEach(() => {
        vi.runAllTimers();
        if (wrapper) {
            wrapper?.unmount();
        }
        document.body.innerHTML = '';
        vi.useRealTimers();
    });

    it('mounts with correct initial state', async () => {
        wrapper = mount(Slug, { props: defaultProps });

        expect(wrapper!.vm!.val).toBe('initial-slug');
        expect(wrapper!.vm!.edit).toBe(false);
        expect(wrapper!.vm!.locked).toBe(true);
        expect(wrapper!.vm!.buttonText).toBe('Locked');

        await wrapper!.vm!.$nextTick();
        const input = wrapper!.find('input');
        expect((input.element as HTMLInputElement).value).toBe('initial-slug');
        expect(input.attributes('readonly')).toBeDefined(); // Since edit is false
        expect(input.attributes('required')).toBeDefined();
    });

    it('shouldGenerateFromTitle handles isNew and title length logic correctly', async () => {
        wrapper = mount(Slug, { props: defaultProps });

        // isNew = true always generates
        await wrapper!.setProps({ isNew: true });
        expect(wrapper!.vm!.shouldGenerateFromTitle('Some title')).toBe(true);
        expect(wrapper!.vm!.shouldGenerateFromTitle('')).toBe(true);

        // isNew = false, title is empty, localize = true
        wrapper = mount(Slug, {
            props: { ...defaultProps, isNew: false, localize: true },
        });
        expect(wrapper!.vm!.shouldGenerateFromTitle('')).toBe(true);

        // isNew = false, title not empty, localize = true
        expect(wrapper!.vm!.shouldGenerateFromTitle('Hello')).toBe(false);

        // isNew = false, title empty, localize = false
        wrapper = mount(Slug, {
            props: { ...defaultProps, isNew: false, localize: false },
        });
        expect(wrapper!.vm!.shouldGenerateFromTitle('')).toBe(false);
    });

    it('generates slug from title in mounted hook if shouldGenerateFromTitle is true', async () => {
        const emitSpy = vi.spyOn(eventBus, 'emit');

        wrapper = mount(Slug, {
            props: { ...defaultProps, isNew: true },
        });

        // Fast-forward the setTimeout(..., 0) inside mounted
        vi.runAllTimers();
        await wrapper!.vm!.$nextTick();

        // Check if generateSlug logic executed correctly
        expect(wrapper!.vm!.icon).toBe('unlock');
        expect(wrapper!.vm!.buttonText).toBe('Unlocked');
        expect(wrapper!.vm!.val).toBe('test-title-subtitle');
        expect(wrapper!.vm!.locked).toBe(false);

        // Check if event was emitted
        expect(emitSpy).toHaveBeenCalledWith('generate-from-title', generatePayload);
    });

    it('edits slug properly', async () => {
        const emitSpy = vi.spyOn(eventBus, 'emit');
        wrapper = mount(Slug, { props: defaultProps });

        wrapper!.vm!.editSlug();

        expect(wrapper!.vm!.edit).toBe(true);
        expect(wrapper!.vm!.locked).toBe(false);
        expect(wrapper!.vm!.buttonText).toBe('Edit');
        expect(wrapper!.vm!.icon).toBe('pencil-alt');
        expect(emitSpy).toHaveBeenCalledWith('generate-from-title', stopGeneratePayload);

        // Input should no longer be readonly
        await wrapper!.vm!.$nextTick();
        const input = wrapper!.find('input');
        expect(input.attributes('readonly')).toBeUndefined();
    });

    it('locks slug properly and slugifies existing value', async () => {
        const emitSpy = vi.spyOn(eventBus, 'emit');
        wrapper = mount(Slug, { props: defaultProps });

        // First set edit to true and provide an unslugified value
        wrapper!.vm!.edit = true;
        wrapper!.vm!.val = 'Unsafe Slug Value @#';

        wrapper!.vm!.lockSlug();

        expect(wrapper!.vm!.edit).toBe(false);
        expect(wrapper!.vm!.locked).toBe(true);
        expect(wrapper!.vm!.buttonText).toBe('Locked');
        expect(wrapper!.vm!.icon).toBe('lock');
        expect(wrapper!.vm!.val).toBe('unsafe-slug-value'); // Slugified
        expect(emitSpy).toHaveBeenCalledWith('generate-from-title', stopGeneratePayload);
    });

    it('updates val when input is changed', async () => {
        wrapper = mount(Slug, { props: defaultProps });

        // Unlock editing first
        wrapper!.vm!.editSlug();
        await wrapper!.vm!.$nextTick();

        const input = wrapper!.find('input');
        await input.setValue('new-manual-slug');

        expect(wrapper!.vm!.val).toBe('new-manual-slug');
    });

    it('generates slug on demand when generateSlug is called directly', () => {
        const emitSpy = vi.spyOn(eventBus, 'emit');
        wrapper = mount(Slug, { props: defaultProps });

        wrapper!.vm!.generateSlug();

        expect(wrapper!.vm!.val).toBe('test-title-subtitle');
        expect(wrapper!.vm!.edit).toBe(false);
        expect(wrapper!.vm!.locked).toBe(false);
        expect(wrapper!.vm!.buttonText).toBe('Unlocked');
        expect(wrapper!.vm!.icon).toBe('unlock');
        expect(emitSpy).toHaveBeenCalledWith('generate-from-title', generatePayload);
    });

    it('ignores missing generate source fields instead of throwing', () => {
        document.body.innerHTML = `
            <input name="fields[title]" value="Only Title" />
        `;

        wrapper = mount(Slug, { props: defaultProps });

        expect(() => wrapper!.vm!.generateSlug()).not.toThrow();
        expect(wrapper!.vm!.val).toBe('only-title');
    });

    it('listens to slugify-from-title event and generates slug', () => {
        const emitSpy = vi.spyOn(eventBus, 'emit');
        wrapper = mount(Slug, { props: defaultProps });

        eventBus.emit('slugify-from-title', { source: 'title' });

        expect(wrapper!.vm!.val).toBe('test-title-subtitle');
        expect(wrapper!.vm!.icon).toBe('unlock');
        expect(emitSpy).toHaveBeenCalledWith('generate-from-title', generatePayload);
    });

    it('ignores slugify-from-title events from unrelated source fields', () => {
        wrapper = mount(Slug, { props: defaultProps });

        eventBus.emit('slugify-from-title', { source: 'other_title' });

        expect(wrapper!.vm!.val).toBe('initial-slug');
    });

    it('unsubscribes from slugify-from-title when destroyed', () => {
        wrapper = mount(Slug, { props: defaultProps });

        // Mounting registers exactly one listener
        expect(eventBus.all.get('slugify-from-title')).toHaveLength(1);

        wrapper?.unmount();

        const listeners = eventBus.all.get('slugify-from-title');
        expect(listeners === undefined || listeners.length === 0).toBe(true); // Map returns undefined when array is empty or removed
        wrapper = null; // Prevent afterEach from unmounting again
    });

    it('renders errormessage and pattern attributes only when they are strings', async () => {
        wrapper = mount(Slug, { props: defaultProps });
        let input = wrapper!.find('input');
        expect(input.attributes('data-errormessage')).toBe('Error!');
        expect(input.attributes('pattern')).toBe('[a-z0-9]+');

        // Twig passes `false` when no errormessage/pattern is configured
        wrapper = mount(Slug, {
            props: { ...defaultProps, errormessage: false, pattern: false },
        });
        input = wrapper!.find('input');
        expect(input.attributes('data-errormessage')).toBeUndefined();
        expect(input.attributes('pattern')).toBeUndefined();
    });

    it('generates an empty slug when the generate prop is empty', () => {
        wrapper = mount(Slug, { props: { ...defaultProps, generate: '' } });

        wrapper!.vm!.generateSlug();

        expect(wrapper!.vm!.val).toBeFalsy();
    });

    it('trigger button clicks properly', async () => {
        wrapper = mount(Slug, { props: defaultProps });

        // Find dropdown edit button
        const buttons = wrapper!.findAll('.dropdown-item');
        // Index 0: Edit, Index 1: Lock (disabled if already locked, but default is locked so only Edit and Generate)

        expect(buttons).toHaveLength(2); // Since locked = true, lock button is hidden via v-if="!locked"
        expect(buttons[0].text()).toContain('Edit');
        expect(buttons[1].text()).toContain('Generate from');

        // Click Edit
        await buttons[0].trigger('click');
        expect(wrapper!.vm!.edit).toBe(true);

        // UI changes: Lock button should appear, Edit button disappears
        await wrapper!.vm!.$nextTick();
        const updatedButtons = wrapper!.findAll('.dropdown-item');
        expect(updatedButtons).toHaveLength(2); // Lock, Generate
        expect(updatedButtons[0].text()).toContain('Locked');

        // Click Lock
        await updatedButtons[0].trigger('click');
        expect(wrapper!.vm!.locked).toBe(true);

        // Click Generate
        const generateButton = wrapper.findAll('.dropdown-item').find(b => b.text().includes('Generate'));
        await generateButton!.trigger('click');
        expect(wrapper!.vm!.val).toBe('test-title-subtitle');
    });
});
