import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { afterEach, describe, expect, it, vi } from 'vitest';
import Embed from '@/editor/Components/Embed.vue';
import { withCsrfToken } from '../helpers/dom';

const labels = {
    content_url: 'Content URL',
    placeholder_content_url: 'Paste a URL',
    refresh: 'Refresh',
    delete: 'Remove',
    label_size: 'Size',
    label_height: 'Height',
    label_pixel: 'px',
    label_matched_embed: 'Matched embed',
    label_preview: 'Preview',
    field_width: 'Width',
    field_height: 'Height',
    field_title: 'Title',
    field_author: 'Author',
};

const defaultProps = {
    embedapi: '/bolt/async/embed',
    name: 'fields[video]',
    authorurl: '',
    authorname: '',
    height: '',
    html: '',
    thumbnail: '',
    title: '',
    url: '',
    width: '',
    labels,
    required: false,
    readonly: false,
    errormessage: 'Not a valid URL',
    pattern: 'https://.*',
};

const oembed = {
    author_url: 'https://example.org/bob',
    author_name: 'Bob',
    height: 360,
    html: '<iframe src="https://example.org/embed/1"></iframe>',
    thumbnail_url: 'https://example.org/thumb.jpg',
    title: 'A video',
    width: 640,
};

const mountEmbed = (props: Record<string, unknown> = {}) => {
    withCsrfToken('token-123');
    return mount(Embed, { propsData: { ...defaultProps, ...props } });
};

const field = (wrapper: ReturnType<typeof mount>, selector: string) =>
    (wrapper.find(selector).element as HTMLInputElement).value;
const urlInput = (wrapper: ReturnType<typeof mount>) => wrapper.find('input[type="url"]');

const stubFetch = (response: unknown) => {
    const fetchMock = vi.fn(() => Promise.resolve({ json: () => Promise.resolve(response) }));
    vi.stubGlobal('fetch', fetchMock);
    return fetchMock;
};

/** Lets the 500ms debounce elapse and the fetch promise chain settle. */
const settleLookup = async () => {
    await vi.advanceTimersByTimeAsync(500);
    await nextTick();
};

afterEach(() => {
    vi.useRealTimers();
    vi.unstubAllGlobals();
});

describe('EditorEmbed', () => {
    it('renders the url field and its metadata', () => {
        const wrapper = mountEmbed();

        expect(wrapper.find('label.form-label').text()).toBe('Content URL');
        expect(urlInput(wrapper).attributes('name')).toBe('fields[video][url]');
        expect(urlInput(wrapper).attributes('placeholder')).toBe('Paste a URL');
        expect(urlInput(wrapper).attributes('data-errormessage')).toBe('Not a valid URL');
        expect(urlInput(wrapper).attributes('pattern')).toBe('https://.*');
    });

    it('posts each part of the embed under its own field name', () => {
        const wrapper = mountEmbed();

        const names = wrapper.findAll('input').wrappers.map(input => input.attributes('name'));
        expect(names).toEqual([
            'fields[video][url]',
            'fields[video][width]',
            'fields[video][height]',
            'fields[video][title]',
            'fields[video][authorname]',
            'fields[video][authorurl]',
            'fields[video][html]',
            'fields[video][thumbnail]',
        ]);
    });

    it('shows the stored embed details', () => {
        const wrapper = mountEmbed({
            url: 'https://example.org/watch/1',
            title: 'A video',
            authorname: 'Bob',
            authorurl: 'https://example.org/bob',
            html: '<iframe></iframe>',
            thumbnail: 'https://example.org/thumb.jpg',
            width: 640,
            height: 360,
        });

        expect(field(wrapper, 'input.title')).toBe('A video');
        expect(field(wrapper, 'input.author_name')).toBe('Bob');
        expect(field(wrapper, 'input.author_url')).toBe('https://example.org/bob');
        expect(field(wrapper, 'input.html')).toBe('<iframe></iframe>');
        expect(field(wrapper, 'input.thumbnail_url')).toBe('https://example.org/thumb.jpg');
        expect(field(wrapper, 'input[name="fields[video][width]"]')).toBe('640');
        expect(field(wrapper, 'input[name="fields[video][height]"]')).toBe('360');
    });

    it('marks the title and author fields read-only, since the api fills them', () => {
        const wrapper = mountEmbed();

        expect(wrapper.find('input.title').attributes('readonly')).toBeDefined();
        expect(wrapper.find('input.author_name').attributes('readonly')).toBeDefined();
    });

    it('shows a preview when there is a thumbnail', () => {
        const wrapper = mountEmbed({ thumbnail: 'https://example.org/thumb.jpg' });

        const preview = wrapper.find('.editor__image--preview-image');
        expect(preview.attributes('href')).toBe('https://example.org/thumb.jpg');
        expect(preview.attributes('style')).toContain('https://example.org/thumb.jpg');
    });

    it('shows no preview without a thumbnail', () => {
        const wrapper = mountEmbed();

        expect(wrapper.find('.editor__image--preview-image').exists()).toBe(false);
    });

    it('looks the url up against the embed api', async () => {
        vi.useFakeTimers();
        const fetchMock = stubFetch(oembed);
        const wrapper = mountEmbed();

        await urlInput(wrapper).setValue('https://example.org/watch/1');
        await settleLookup();

        expect(fetchMock).toHaveBeenCalledTimes(1);
        const [url, init] = fetchMock.mock.calls[0] as unknown as [string, { method: string; body: FormData }];
        expect(url).toBe('/bolt/async/embed');
        expect(init.method).toBe('POST');
        expect(init.body.get('url')).toBe('https://example.org/watch/1');
        expect(init.body.get('_csrf_token')).toBe('token-123');
    });

    it('waits for typing to stop before looking anything up', async () => {
        vi.useFakeTimers();
        const fetchMock = stubFetch(oembed);
        const wrapper = mountEmbed();

        await urlInput(wrapper).setValue('https://example.org/w');
        await vi.advanceTimersByTimeAsync(200);
        await urlInput(wrapper).setValue('https://example.org/watch/1');
        await vi.advanceTimersByTimeAsync(200);
        expect(fetchMock).not.toHaveBeenCalled();

        await settleLookup();

        expect(fetchMock).toHaveBeenCalledTimes(1);
        const [, init] = fetchMock.mock.calls[0] as unknown as [string, { body: FormData }];
        expect(init.body.get('url')).toBe('https://example.org/watch/1');
    });

    it('fills the embed fields in from the api response', async () => {
        vi.useFakeTimers();
        stubFetch(oembed);
        const wrapper = mountEmbed();

        await urlInput(wrapper).setValue('https://example.org/watch/1');
        await settleLookup();

        expect(field(wrapper, 'input.title')).toBe('A video');
        expect(field(wrapper, 'input.author_name')).toBe('Bob');
        expect(field(wrapper, 'input.author_url')).toBe('https://example.org/bob');
        expect(field(wrapper, 'input.html')).toBe(oembed.html);
        expect(field(wrapper, 'input.thumbnail_url')).toBe('https://example.org/thumb.jpg');
        expect(field(wrapper, 'input[name="fields[video][width]"]')).toBe('640');
        expect(field(wrapper, 'input[name="fields[video][height]"]')).toBe('360');
    });

    it('spins the refresh button while a lookup is in flight', async () => {
        vi.useFakeTimers();
        stubFetch(oembed);
        const wrapper = mountEmbed();

        await urlInput(wrapper).setValue('https://example.org/watch/1');
        await nextTick();
        expect(wrapper.find('button.refresh').attributes('disabled')).toBeDefined();
        expect(wrapper.find('button.refresh i').classes()).toContain('fa-spin');

        await settleLookup();

        expect(wrapper.find('button.refresh').attributes('disabled')).toBeUndefined();
        expect(wrapper.find('button.refresh i').classes()).not.toContain('fa-spin');
    });

    it('looks the url up again when refresh is pressed', async () => {
        vi.useFakeTimers();
        const fetchMock = stubFetch(oembed);
        const wrapper = mountEmbed({ url: 'https://example.org/watch/1' });
        await settleLookup();
        expect(fetchMock).toHaveBeenCalledTimes(1);

        await wrapper.find('button.refresh').trigger('click');
        await settleLookup();

        expect(fetchMock).toHaveBeenCalledTimes(2);
    });

    it('looks up a stored url on creation', async () => {
        vi.useFakeTimers();
        const fetchMock = stubFetch(oembed);
        mountEmbed({ url: 'https://example.org/watch/1' });

        await settleLookup();

        expect(fetchMock).toHaveBeenCalledTimes(1);
    });

    it('stays quiet when there is no url', async () => {
        vi.useFakeTimers();
        const fetchMock = stubFetch(oembed);
        mountEmbed();

        await settleLookup();

        expect(fetchMock).not.toHaveBeenCalled();
    });

    it('clears the url with the remove button', async () => {
        vi.useFakeTimers();
        stubFetch(oembed);
        const wrapper = mountEmbed({ url: 'https://example.org/watch/1' });
        await settleLookup();

        await wrapper.find('button.remove').trigger('click');

        expect((urlInput(wrapper).element as HTMLInputElement).value).toBe('');
    });

    it('reports a failed lookup without breaking the field', async () => {
        vi.useFakeTimers();
        const warn = vi.spyOn(console, 'warn').mockImplementation(() => undefined);
        vi.stubGlobal(
            'fetch',
            vi.fn(() => Promise.reject(new Error('offline'))),
        );
        const wrapper = mountEmbed();

        await urlInput(wrapper).setValue('https://example.org/watch/1');
        await settleLookup();

        expect(warn).toHaveBeenCalled();
        expect(wrapper.find('button.refresh').attributes('disabled')).toBeUndefined();
        expect(field(wrapper, 'input.title')).toBe('');
        warn.mockRestore();
    });

    it('refreshes the preview after a lookup', async () => {
        // Note `previewImage` is assigned in created() but never declared in
        // data(), so Vue does not track it. This works only because the same
        // response also writes the declared fields, and the re-render they
        // trigger happens to re-read previewImage. Anything that updated the
        // preview on its own would not show up.
        vi.useFakeTimers();
        stubFetch(oembed);
        const wrapper = mountEmbed();

        await urlInput(wrapper).setValue('https://example.org/watch/1');
        await settleLookup();

        expect(field(wrapper, 'input.thumbnail_url')).toBe('https://example.org/thumb.jpg');
        expect(wrapper.find('.editor__image--preview-image').attributes('href')).toBe('https://example.org/thumb.jpg');
    });

    it('marks the url field read-only without disabling remove', () => {
        const wrapper = mountEmbed({ readonly: true });

        expect(urlInput(wrapper).attributes('readonly')).toBeDefined();
        expect(wrapper.find('input[name="fields[video][width]"]').attributes('readonly')).toBeDefined();
        expect(wrapper.find('button.remove').attributes('disabled')).toBeUndefined();
    });
});
