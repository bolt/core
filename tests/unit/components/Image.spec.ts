import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { afterEach, describe, expect, it, vi } from 'vitest';
import Axios from 'axios';
import ImageField from '@/editor/Components/Image.vue';
import { renable } from '@/patience-is-a-virtue';
import { resetModalContent } from '@/modal';
import { withResourcesModal } from '../helpers/dom';
import { flushPromises } from '../helpers/async';
import { expectVueWarning } from '../helpers/warnings';

vi.mock('axios', () => ({ default: { get: vi.fn(), post: vi.fn() } }));
vi.mock('@/patience-is-a-virtue', () => ({ renable: vi.fn() }));
vi.mock('@/modal', () => ({ resetModalContent: vi.fn() }));
vi.mock('bootstrap', () => ({
    Modal: { getOrCreateInstance: vi.fn(() => ({ hide: vi.fn() })) },
}));
vi.mock('no-scroll', () => ({ default: { on: vi.fn(), off: vi.fn() } }));
vi.mock('baguettebox.js', () => ({ default: { run: vi.fn() } }));

const labels = {
    placeholder_filename: 'No image selected',
    placeholder_alt_text: 'Describe the image',
    image_preview: 'Image preview',
    button_upload: 'Upload',
    button_upload_options: 'Upload options',
    button_from_library: 'From library',
    button_from_url: 'From URL',
    button_edit_attributes: 'Edit attributes',
    button_move_up: 'Move up',
    button_move_down: 'Move down',
    button_remove: 'Remove',
    modal_title_images: 'Select an image',
    modal_title_upload_from_url: 'Upload from URL',
};

const defaultProps = {
    filename: 'photo.jpg',
    name: 'fields[image]',
    id: 'field-image',
    required: false,
    readonly: false,
    thumbnail: '/thumbs/400×300/photo.jpg',
    alt: 'A photo',
    includeAlt: true,
    directory: '/bolt/upload/images',
    directoryurl: '/bolt/upload/url',
    media: '',
    csrfToken: 'token-123',
    labels,
    filelist: '/bolt/async/filelist',
    extensions: ['jpg', 'png'],
    attributesLink: '/bolt/attributes',
    inImagelist: false,
    isFirstInImagelist: false,
    isLastInImagelist: false,
    extraFields: [],
    extraData: [],
};

const mountImage = (props: Record<string, unknown> = {}) =>
    mount(ImageField, { propsData: { ...defaultProps, ...props } });

const value = (wrapper: ReturnType<typeof mount>, selector: string) =>
    (wrapper.find(selector).element as HTMLInputElement).value;
const filenameInput = 'input[name="fields[image][filename]"]';
const altInput = 'input[name="fields[image][alt]"]';
const preview = (wrapper: ReturnType<typeof mount>) => wrapper.find('.editor__image--preview-image');
const button = (wrapper: ReturnType<typeof mount>, label: string) =>
    wrapper.findAll('button').wrappers.find(b => b.text().trim() === label)!;

const serverImages = [
    { text: 'a.jpg', group: 'files', value: 'files/a.jpg', base_url_path: '/bolt/async/filelist' },
    { text: 'skip.exe', group: 'files', value: 'files/skip.exe', base_url_path: '/bolt/async/filelist' },
];

afterEach(() => {
    vi.mocked(Axios.get).mockReset();
    vi.mocked(Axios.post).mockReset();
    vi.mocked(renable).mockReset();
    vi.mocked(resetModalContent).mockReset();
});

describe('EditorImage', () => {
    it('renders the filename with its field name', async () => {
        const wrapper = mountImage();
        await nextTick();

        expect(value(wrapper, filenameInput)).toBe('photo.jpg');
        expect(wrapper.find(filenameInput).attributes('title')).toBe('fields[image] filename');
        expect(wrapper.find(filenameInput).attributes('placeholder')).toBe('No image selected');
    });

    it('derives the preview and thumbnail urls from the filename', async () => {
        const wrapper = mountImage();
        await nextTick();

        expect(preview(wrapper).attributes('href')).toBe('/thumbs/1000×1000/photo.jpg');
        expect(preview(wrapper).attributes('style')).toContain('/thumbs/400×300/photo.jpg');
        expect(preview(wrapper).find('.sr-only').text()).toBe('Image preview');
    });

    it('shows no preview when there is no image', async () => {
        const wrapper = mountImage({ filename: '' });
        await nextTick();

        expect(preview(wrapper).exists()).toBe(false);
    });

    it('renders the alt field when asked to', async () => {
        const wrapper = mountImage();
        await nextTick();

        expect(value(wrapper, altInput)).toBe('A photo');
        expect(wrapper.find(altInput).attributes('placeholder')).toBe('Describe the image');
    });

    it('omits the alt field when not asked to', () => {
        const wrapper = mountImage({ includeAlt: false });

        expect(wrapper.find(altInput).exists()).toBe(false);
    });

    it('prefers an explicit placeholder for the alt text', () => {
        const wrapper = mountImage({ placeholder: 'Custom placeholder' });

        expect(wrapper.find(altInput).attributes('placeholder')).toBe('Custom placeholder');
    });

    it('renders an input per extra field, bound to the record', async () => {
        // `extraFields` and `extraData` are declared as Array, but the template
        // iterates the first as an object and keys into the second by field
        // name — so real usage always trips Vue's prop-type check. Imagelist.vue
        // passes objects too; the declarations are simply wrong.
        expectVueWarning(/Invalid prop: type check failed for prop "extraFields"/);
        expectVueWarning(/Invalid prop: type check failed for prop "extraData"/);
        const wrapper = mountImage({
            extraFields: { caption: { label: 'Caption', placeholder: 'Say something' } },
            extraData: { caption: 'A caption' },
        });
        await nextTick();

        const caption = wrapper.find('input[name="fields[image][caption]"]');
        expect(caption.exists()).toBe(true);
        expect((caption.element as HTMLInputElement).value).toBe('A caption');
        expect(caption.attributes('placeholder')).toBe('Say something');
        expect(wrapper.findAll('label').wrappers.map(l => l.text())).toContain('Caption:');
    });

    it('offers the attributes link only once an image is chosen', async () => {
        const withImage = mountImage();
        await nextTick();
        expect(withImage.find('a.dropdown-item').attributes('href')).toBe('/bolt/attributes?file=photo.jpg');

        const empty = mountImage({ filename: '' });
        await nextTick();
        expect(empty.find('a.dropdown-item').exists()).toBe(false);
    });

    it('hides the reordering buttons outside an image list', () => {
        const wrapper = mountImage({ inImagelist: false });

        expect(button(wrapper, 'Move up')).toBeUndefined();
        expect(button(wrapper, 'Move down')).toBeUndefined();
    });

    it('asks its list to move it up and down', async () => {
        const wrapper = mountImage({ inImagelist: true });

        await button(wrapper, 'Move up').trigger('click');
        await button(wrapper, 'Move down').trigger('click');

        expect(wrapper.emitted('move-image-up')).toHaveLength(1);
        expect(wrapper.emitted('move-image-down')).toHaveLength(1);
    });

    it('cannot move the first image up or the last one down', () => {
        const first = mountImage({ inImagelist: true, isFirstInImagelist: true });
        expect(button(first, 'Move up').attributes('disabled')).toBeDefined();

        const last = mountImage({ inImagelist: true, isLastInImagelist: true });
        expect(button(last, 'Move down').attributes('disabled')).toBeDefined();
    });

    it('clears the image, its alt text and its preview when removed', async () => {
        const wrapper = mountImage();
        await nextTick();

        await button(wrapper, 'Remove').trigger('click');

        expect(value(wrapper, filenameInput)).toBe('');
        expect(value(wrapper, altInput)).toBe('');
        expect(preview(wrapper).exists()).toBe(false);
        expect(wrapper.emitted('remove')).toHaveLength(1);
    });

    it('keeps the alt text when the field does not show one', async () => {
        const wrapper = mountImage({ includeAlt: false });
        await nextTick();

        await button(wrapper, 'Remove').trigger('click');

        expect(wrapper.emitted('remove')).toHaveLength(1);
        expect(value(wrapper, filenameInput)).toBe('');
    });

    it('disables every action when read-only', () => {
        const wrapper = mountImage({ readonly: true });

        expect(button(wrapper, 'Upload').attributes('disabled')).toBeDefined();
        expect(button(wrapper, 'Remove').attributes('disabled')).toBeDefined();
        expect(button(wrapper, 'From URL').attributes('disabled')).toBeDefined();
        expect(wrapper.find(altInput).attributes('readonly')).toBeDefined();
    });

    describe('uploading', () => {
        const file = new File(['bytes'], 'new.jpg', { type: 'image/jpeg' });

        it('posts the image and shows the new preview', async () => {
            vi.mocked(Axios.post).mockResolvedValue({ data: 'new.jpg' });
            const wrapper = mountImage();
            await nextTick();

            await wrapper.find('.editor__image').trigger('drop', { dataTransfer: { files: [file] } });
            await flushPromises();
            await nextTick();

            const [url, body] = vi.mocked(Axios.post).mock.calls[0] as unknown as [string, FormData];
            expect(url).toBe('/bolt/upload/images');
            expect(body.get('image')).toBe(file);
            expect(body.get('_csrf_token')).toBe('token-123');
            expect(value(wrapper, filenameInput)).toBe('new.jpg');
            expect(preview(wrapper).attributes('href')).toBe('/thumbs/1000×1000/new.jpg');
        });

        it('shows upload progress and clears it when the upload finishes', async () => {
            let reportProgress: ((event: { loaded: number; total: number }) => void) | undefined;
            let finishUpload!: (response: { data: string }) => void;
            vi.mocked(Axios.post).mockImplementation((_url, _body, config) => {
                reportProgress = (config as { onUploadProgress: typeof reportProgress }).onUploadProgress;
                return new Promise(resolve => {
                    finishUpload = resolve;
                }) as never;
            });
            const wrapper = mountImage();
            await nextTick();

            await wrapper.find('.editor__image').trigger('drop', { dataTransfer: { files: [file] } });
            reportProgress!({ loaded: 75, total: 100 });
            await nextTick();

            expect(wrapper.find('.progress-bar').attributes('aria-valuenow')).toBe('75');

            finishUpload({ data: 'new.jpg' });
            await flushPromises();
            await nextTick();

            expect(wrapper.find('.progress').exists()).toBe(false);
        });

        it('warns and keeps the old image when the upload fails', async () => {
            const alert = vi.spyOn(window, 'alert').mockImplementation(() => undefined);
            const warn = vi.spyOn(console, 'warn').mockImplementation(() => undefined);
            vi.mocked(Axios.post).mockRejectedValue({
                response: { data: { error: { message: 'Image too large' } } },
            });
            const wrapper = mountImage();
            await nextTick();

            await wrapper.find('.editor__image').trigger('drop', { dataTransfer: { files: [file] } });
            await flushPromises();
            await nextTick();

            expect(alert).toHaveBeenCalledWith('Image too large');
            expect(value(wrapper, filenameInput)).toBe('photo.jpg');
            alert.mockRestore();
            warn.mockRestore();
        });

        // Not covered: an upload failure carrying no structured message. Unlike
        // File.vue, this handler reaches straight for
        // err.response.data.error.message with no fallback, so a plain network
        // error throws inside the catch. Asserting that means leaving an
        // unhandled rejection behind, which Vitest surfaces as a run-level
        // failure, so it is left to the Vue 3 branch to fix and cover.

        it('highlights the field while an image is dragged over it', async () => {
            const wrapper = mountImage();
            const dropZone = wrapper.find('.editor__image');

            await dropZone.trigger('dragenter');
            expect(wrapper.find('.editor__image--dragging').isVisible()).toBe(true);

            await dropZone.trigger('dragleave');
            expect(wrapper.find('.editor__image--dragging').isVisible()).toBe(false);
        });
    });

    describe('browsing the server', () => {
        it('lists the allowed server images in the modal', async () => {
            vi.mocked(Axios.get).mockResolvedValue({ data: serverImages });
            const wrapper = mountImage();
            await nextTick();
            withResourcesModal();

            await button(wrapper, 'From library').trigger('click');
            await flushPromises();

            expect(Axios.get).toHaveBeenCalledWith('/bolt/async/filelist');
            const body = document.querySelector('.modal-body')!;
            expect(body.innerHTML).toContain('a.jpg');
            expect(body.innerHTML).not.toContain('skip.exe');
            expect(document.querySelector('.modal-title')!.innerHTML).toBe('Select an image');
        });

        it('takes the chosen image when the modal closes', async () => {
            vi.mocked(Axios.get).mockResolvedValue({ data: serverImages });
            const wrapper = mountImage();
            await nextTick();
            withResourcesModal();
            await button(wrapper, 'From library').trigger('click');
            await flushPromises();

            const checkbox = document.querySelector('.modal-body input[type=checkbox]') as HTMLInputElement;
            checkbox.checked = true;
            document.getElementById('resourcesModal')!.dispatchEvent(new Event('hidden.bs.modal'));
            await nextTick();

            expect(value(wrapper, filenameInput)).toBe('a.jpg');
            expect(preview(wrapper).attributes('href')).toBe('/thumbs/1000×1000/a.jpg');
            expect(resetModalContent).toHaveBeenCalledWith(labels);
        });

        it('renders a folder as a link rather than a selectable image', async () => {
            vi.mocked(Axios.get).mockResolvedValue({
                data: [
                    { text: 'sub', group: 'directories', value: 'files/sub/', base_url_path: '/bolt/async/filelist' },
                    ...serverImages,
                ],
            });
            const wrapper = mountImage();
            await nextTick();
            withResourcesModal();

            await button(wrapper, 'From library').trigger('click');
            await flushPromises();

            // The first link is the "up one level" one; the folder itself follows.
            const links = Array.from(document.querySelectorAll('.modal-body a.directory')) as HTMLAnchorElement[];
            expect(links.map(link => link.href)).toContainEqual(
                expect.stringContaining('location=files/sub/&type=images'),
            );
        });

        it('lists the chosen folder when a directory is opened', async () => {
            vi.mocked(Axios.get).mockResolvedValue({
                data: [
                    { text: 'sub', group: 'directories', value: 'files/sub/', base_url_path: '/bolt/async/filelist' },
                    ...serverImages,
                ],
            });
            const wrapper = mountImage();
            await nextTick();
            withResourcesModal();
            await button(wrapper, 'From library').trigger('click');
            await flushPromises();

            vi.mocked(Axios.get).mockResolvedValue({
                data: [
                    {
                        text: 'deep.jpg',
                        group: 'files',
                        value: 'files/sub/deep.jpg',
                        base_url_path: '/bolt/async/filelist',
                    },
                ],
            });
            const links = Array.from(document.querySelectorAll('.modal-body a.directory')) as HTMLAnchorElement[];
            links[links.length - 1].click();
            await flushPromises();

            expect(document.querySelector('.modal-body')!.innerHTML).toContain('deep.jpg');
            expect(document.querySelector('.modal-title')!.innerHTML).toContain('files/sub');
        });

        it('reports a failed file list and re-enables the button', async () => {
            const alert = vi.spyOn(window, 'alert').mockImplementation(() => undefined);
            vi.mocked(Axios.get).mockRejectedValue({ response: { data: 'Could not read the directory' } });
            const wrapper = mountImage();
            await nextTick();
            withResourcesModal();

            await button(wrapper, 'From library').trigger('click');
            await flushPromises();

            expect(alert).toHaveBeenCalledWith('Could not read the directory<br>Image did not upload.');
            expect(renable).toHaveBeenCalled();
            alert.mockRestore();
        });
    });

    describe('uploading from a url', () => {
        const openUrlModal = async (wrapper: ReturnType<typeof mount>) => {
            withResourcesModal();
            await button(wrapper, 'From URL').trigger('click');
            // The url form is written into the modal on a 1ms timeout.
            await flushPromises();
        };

        it('offers a url form in the modal', async () => {
            const wrapper = mountImage();
            await nextTick();

            await openUrlModal(wrapper);

            expect(document.querySelector('.modal-title')!.innerHTML).toBe('Upload from URL');
            expect(document.querySelector('input[name=from-url-input]')).not.toBeNull();
        });

        it('fetches the image from the url when accepted', async () => {
            vi.mocked(Axios.post).mockResolvedValue({ data: 'fetched.jpg' });
            const wrapper = mountImage();
            await nextTick();
            await openUrlModal(wrapper);

            const urlInput = document.querySelector('input[name=from-url-input]') as HTMLInputElement;
            urlInput.value = 'https://example.org/remote.jpg';
            (document.getElementById('modalButtonAccept') as HTMLButtonElement).click();
            await flushPromises();
            await nextTick();

            const [url, body] = vi.mocked(Axios.post).mock.calls[0] as unknown as [string, FormData];
            expect(url).toBe('/bolt/upload/url');
            expect(body.get('url')).toBe('https://example.org/remote.jpg');
            expect(body.get('_csrf_token')).toBe('token-123');
            expect(value(wrapper, filenameInput)).toBe('fetched.jpg');
        });

        it('does nothing when the url is left blank', async () => {
            const wrapper = mountImage();
            await nextTick();
            await openUrlModal(wrapper);

            (document.getElementById('modalButtonAccept') as HTMLButtonElement).click();
            await flushPromises();

            expect(Axios.post).not.toHaveBeenCalled();
        });

        it('reports a failed fetch', async () => {
            const alert = vi.spyOn(window, 'alert').mockImplementation(() => undefined);
            const warn = vi.spyOn(console, 'warn').mockImplementation(() => undefined);
            vi.mocked(Axios.post).mockRejectedValue({
                response: { data: { error: { message: 'Could not fetch that URL' } } },
            });
            const wrapper = mountImage();
            await nextTick();
            await openUrlModal(wrapper);

            const urlInput = document.querySelector('input[name=from-url-input]') as HTMLInputElement;
            urlInput.value = 'https://example.org/missing.jpg';
            (document.getElementById('modalButtonAccept') as HTMLButtonElement).click();
            await flushPromises();

            expect(alert).toHaveBeenCalledWith('Could not fetch that URL');
            expect(value(wrapper, filenameInput)).toBe('photo.jpg');
            alert.mockRestore();
            warn.mockRestore();
        });
    });

    it.skip('binds a declared extra field when passed a whole image record', () => {
        // Only passes after 40d34700. Today the whole record is handed to
        // `extra-data`, so keys of the base record (filename, alt, media…) leak
        // into the extra-field inputs when an extra field shares their name.
    });

    it.skip('surfaces the server message when a browse failure is a real AxiosError', () => {
        // Only passes after 9f243336, which moves the file browser into
        // editor/utils/serverFileBrowser.ts and reads the server's message.
        // That module does not exist on this branch.
    });
});
