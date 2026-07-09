import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import Embed from '@/editor/Components/Embed.vue';
import baguetteBox from 'baguettebox.js';
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';

vi.mock('baguettebox.js', () => ({ default: { run: vi.fn() } }));

describe('EditorEmbed Component', () => {
    const embedResponse = {
        author_url: 'https://example.org/author',
        author_name: 'Some Author',
        height: 315,
        html: '<iframe src="https://example.org/embed"></iframe>',
        thumbnail_url: 'https://example.org/thumb.jpg',
        title: 'Some Video',
        width: 560,
    };
    type EmbedFetch = (
        input: RequestInfo | URL,
        init?: RequestInit,
    ) => Promise<{ json: () => Promise<typeof embedResponse> }>;
    let wrapper: VueWrapper<ComponentPublicInstance>;
    let fetchMock: ReturnType<typeof vi.fn<EmbedFetch>>;

    const defaultProps = {
        embedapi: '/async/embed',
        name: 'fields[embed]',
        authorurl: 'https://example.org/old-author',
        authorname: 'Old Author',
        height: 200,
        html: '<iframe src="https://example.org/old"></iframe>',
        thumbnail: 'https://example.org/old-thumb.jpg',
        title: 'Old Video',
        url: '',
        width: 400,
        labels: {
            content_url: 'Content URL',
            placeholder_content_url: 'Enter a URL',
            label_height: 'Height',
            label_pixel: 'pixels',
            label_matched_embed: 'Matched embed',
            label_preview: 'Preview',
            label_size: 'Size',
            delete: 'Delete',
            refresh: 'Refresh',
            field_width: 'Width',
            field_height: 'Height',
            field_title: 'Title',
            field_author: 'Author',
        },
        required: false,
        readonly: false,
        errormessage: 'Error!',
        pattern: 'https://.+',
    };

    beforeEach(() => {
        vi.useFakeTimers();
        document.body.innerHTML = '<input name="_csrf_token" value="csrf123" />';

        fetchMock = vi.fn(() => Promise.resolve({ json: () => Promise.resolve(embedResponse) }));
        vi.stubGlobal('fetch', fetchMock);
    });

    afterEach(() => {
        wrapper.unmount();
        document.body.innerHTML = '';
        vi.unstubAllGlobals();
        vi.useRealTimers();
        vi.restoreAllMocks();
    });

    const urlInput = () => wrapper.find('input[type="url"]');

    it('renders all sub-inputs from the initial props', () => {
        wrapper = mount(Embed, { props: defaultProps });

        expect((urlInput().element as HTMLInputElement).value).toBe('');
        expect((wrapper.find('input[name="fields[embed][width]"]').element as HTMLInputElement).value).toBe('400');
        expect((wrapper.find('input[name="fields[embed][height]"]').element as HTMLInputElement).value).toBe('200');
        expect((wrapper.find('input[name="fields[embed][title]"]').element as HTMLInputElement).value).toBe(
            'Old Video',
        );
        expect((wrapper.find('input[name="fields[embed][authorname]"]').element as HTMLInputElement).value).toBe(
            'Old Author',
        );
        expect((wrapper.find('input[name="fields[embed][authorurl]"]').element as HTMLInputElement).value).toBe(
            'https://example.org/old-author',
        );
        expect((wrapper.find('input[name="fields[embed][html]"]').element as HTMLInputElement).value).toBe(
            '<iframe src="https://example.org/old"></iframe>',
        );
        expect((wrapper.find('input[name="fields[embed][thumbnail]"]').element as HTMLInputElement).value).toBe(
            'https://example.org/old-thumb.jpg',
        );
        expect(wrapper.text()).toContain('Content URL');
        expect(urlInput().attributes('data-errormessage')).toBe('Error!');
        expect(urlInput().attributes('pattern')).toBe('https://.+');
    });

    it('shows the thumbnail preview when one is set', () => {
        wrapper = mount(Embed, { props: defaultProps });

        const preview = wrapper.find('.editor__image--preview-image');
        expect(preview.exists()).toBe(true);
        expect(preview.attributes('href')).toBe('https://example.org/old-thumb.jpg');
    });

    it('hides the thumbnail preview when none is set', () => {
        wrapper = mount(Embed, { props: { ...defaultProps, thumbnail: '' } });
        expect(wrapper.find('.editor__image--preview-image').exists()).toBe(false);
    });

    it('omits errormessage and pattern when they are false', () => {
        wrapper = mount(Embed, {
            props: { ...defaultProps, errormessage: false, pattern: false },
        });

        expect(urlInput().attributes('data-errormessage')).toBeUndefined();
        expect(urlInput().attributes('pattern')).toBeUndefined();
    });

    it('does not fetch embed data on create when the url is empty', async () => {
        wrapper = mount(Embed, { props: defaultProps });

        await vi.advanceTimersByTimeAsync(1000);

        expect(fetchMock).not.toHaveBeenCalled();
    });

    it('fetches embed data on create when a url is set', async () => {
        wrapper = mount(Embed, {
            props: { ...defaultProps, url: 'https://youtu.be/xyz' },
        });

        // Loading state is active while the debounced fetch is pending
        const refresh = wrapper.find('button.refresh');
        expect(refresh.attributes('disabled')).toBeDefined();
        expect(refresh.find('i').classes()).toContain('fa-spin');

        await vi.advanceTimersByTimeAsync(600);

        expect(fetchMock).toHaveBeenCalledTimes(1);
        expect(fetchMock).toHaveBeenCalledWith('/async/embed', expect.objectContaining({ method: 'POST' }));
        const body = fetchMock.mock.calls[0]?.[1]?.body as FormData;
        expect(body.get('url')).toBe('https://youtu.be/xyz');
        expect(body.get('_csrf_token')).toBe('csrf123');

        await wrapper.vm.$nextTick();

        expect((wrapper.find('input[name="fields[embed][title]"]').element as HTMLInputElement).value).toBe(
            'Some Video',
        );
        expect((wrapper.find('input[name="fields[embed][authorname]"]').element as HTMLInputElement).value).toBe(
            'Some Author',
        );
        expect((wrapper.find('input[name="fields[embed][authorurl]"]').element as HTMLInputElement).value).toBe(
            'https://example.org/author',
        );
        expect((wrapper.find('input[name="fields[embed][width]"]').element as HTMLInputElement).value).toBe('560');
        expect((wrapper.find('input[name="fields[embed][height]"]').element as HTMLInputElement).value).toBe('315');
        expect((wrapper.find('input[name="fields[embed][thumbnail]"]').element as HTMLInputElement).value).toBe(
            'https://example.org/thumb.jpg',
        );
        expect(wrapper.find('.editor__image--preview-image').attributes('href')).toBe('https://example.org/thumb.jpg');

        // Loading state is cleared again
        expect(wrapper.find('button.refresh').attributes('disabled')).toBeUndefined();
        expect(wrapper.find('button.refresh i').classes()).not.toContain('fa-spin');
    });

    it('sends an empty url when the url prop is unset', async () => {
        wrapper = mount(Embed, { props: { ...defaultProps, url: undefined } });

        await wrapper.find('button.refresh').trigger('click');
        await vi.advanceTimersByTimeAsync(600);

        expect((fetchMock.mock.calls[0]?.[1]?.body as FormData).get('url')).toBe('');
    });

    it('fetches embed data when the url is edited', async () => {
        wrapper = mount(Embed, { props: defaultProps });

        await urlInput().setValue('https://vimeo.com/123');
        await vi.advanceTimersByTimeAsync(600);

        expect(fetchMock).toHaveBeenCalledTimes(1);
        expect((fetchMock.mock.calls[0]?.[1]?.body as FormData).get('url')).toBe('https://vimeo.com/123');
    });

    it('does not reinitialize the lightbox for url edits before the preview changes', async () => {
        wrapper = mount(Embed, { props: defaultProps });
        await wrapper.vm.$nextTick();
        vi.mocked(baguetteBox.run).mockClear();

        await urlInput().setValue('https://vimeo.com/123');
        await wrapper.vm.$nextTick();

        expect(baguetteBox.run).not.toHaveBeenCalled();
    });

    it('debounces rapid url edits into a single fetch', async () => {
        wrapper = mount(Embed, { props: defaultProps });

        await urlInput().setValue('https://vimeo.com/1');
        await vi.advanceTimersByTimeAsync(200);
        await urlInput().setValue('https://vimeo.com/12');
        await vi.advanceTimersByTimeAsync(600);

        expect(fetchMock).toHaveBeenCalledTimes(1);
        expect((fetchMock.mock.calls[0]?.[1]?.body as FormData).get('url')).toBe('https://vimeo.com/12');
    });

    it('refetches when the refresh button is clicked', async () => {
        wrapper = mount(Embed, { props: defaultProps });

        await wrapper.find('button.refresh').trigger('click');
        await vi.advanceTimersByTimeAsync(600);

        expect(fetchMock).toHaveBeenCalledTimes(1);
    });

    it('clears the url when the remove button is clicked', async () => {
        wrapper = mount(Embed, {
            props: { ...defaultProps, url: 'https://youtu.be/xyz' },
        });
        await vi.advanceTimersByTimeAsync(600);

        await wrapper.find('button.remove').trigger('click');

        expect((urlInput().element as HTMLInputElement).value).toBe('');
    });

    it('warns and clears the loading state when the fetch fails', async () => {
        const warnSpy = vi.spyOn(console, 'warn').mockImplementation(() => {});
        fetchMock.mockImplementation(() => Promise.reject(new Error('network down')));

        wrapper = mount(Embed, {
            props: { ...defaultProps, url: 'https://youtu.be/xyz' },
        });
        await vi.advanceTimersByTimeAsync(600);
        await wrapper.vm.$nextTick();

        expect(warnSpy).toHaveBeenCalled();
        expect(wrapper.find('button.refresh').attributes('disabled')).toBeUndefined();
    });

    it('marks the url input readonly when readonly', () => {
        wrapper = mount(Embed, { props: { ...defaultProps, readonly: true } });

        expect(urlInput().attributes('readonly')).toBeDefined();
        expect(wrapper.find('input[name="fields[embed][width]"]').attributes('readonly')).toBeDefined();
        expect(wrapper.find('input[name="fields[embed][height]"]').attributes('readonly')).toBeDefined();
    });
});
