import { flushPromises, mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { afterEach, describe, expect, it, vi } from 'vitest';
import Axios from 'axios';
import ImageField from '@/editor/Components/Image.vue';
import baguetteBox from 'baguettebox.js';
import noScroll from 'no-scroll';
import { renable } from '@/patience-is-a-virtue';
import { resetModalContent } from '@/modal';
import { withResourcesModal } from '../helpers/dom';

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
    extraFields: {},
    extraData: {},
};

const mountImage = (props: Record<string, unknown> = {}, options: { attachTo?: HTMLElement } = {}) =>
    mount(ImageField, { props: { ...defaultProps, ...props }, ...options });

const value = (wrapper: ReturnType<typeof mount>, selector: string) =>
    (wrapper.find(selector).element as HTMLInputElement).value;
const filenameInput = 'input[name="fields[image][filename]"]';
const altInput = 'input[name="fields[image][alt]"]';
const preview = (wrapper: ReturnType<typeof mount>) => wrapper.find('.editor__image--preview-image');
const button = (wrapper: ReturnType<typeof mount>, label: string) =>
    wrapper.findAll('button').find(b => b.text().trim() === label)!;

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
        const wrapper = mountImage({
            extraFields: { caption: { label: 'Caption', placeholder: 'Say something' } },
            extraData: { caption: 'A caption' },
        });
        await nextTick();

        const caption = wrapper.find('input[name="fields[image][caption]"]');
        expect(caption.exists()).toBe(true);
        expect((caption.element as HTMLInputElement).value).toBe('A caption');
        expect(caption.attributes('placeholder')).toBe('Say something');
        expect(wrapper.findAll('label').map(l => l.text())).toContain('Caption:');
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

        expect(wrapper.emitted('moveImageUp')).toHaveLength(1);
        expect(wrapper.emitted('moveImageDown')).toHaveLength(1);
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

        // The alt text is not on screen, so reveal the field to read it back.
        await wrapper.setProps({ includeAlt: true });
        expect(value(wrapper, altInput)).toBe('A photo');
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
            vi.spyOn(console, 'warn').mockImplementation(() => undefined);
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
        });

        // Not covered: an upload failure carrying no structured message. Unlike
        // File.vue, this handler reaches straight for
        // err.response.data.error.message with no fallback, so a plain network
        // error throws inside the catch. Asserting that means leaving an
        // unhandled rejection behind, which Vitest surfaces as a run-level
        // failure, so it is left to the Vue 3 branch to fix and cover.

        it('highlights the field while an image is dragged over it', async () => {
            // Attached: isVisible() only reads the real style of an element in the document.
            const wrapper = mountImage({}, { attachTo: document.body });
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
        });

        it('surfaces the server message when the failure is a real AxiosError', async () => {
            const alert = vi.spyOn(window, 'alert').mockImplementation(() => undefined);
            // A genuine AxiosError is an Error that also carries `.response`; its
            // generic message must not shadow the one the server sent.
            vi.mocked(Axios.get).mockRejectedValue(
                Object.assign(new Error('Request failed with status code 400'), {
                    response: { data: { error: { message: 'Server says no' } } },
                }),
            );
            const wrapper = mountImage();
            await nextTick();
            withResourcesModal();

            await button(wrapper, 'From library').trigger('click');
            await flushPromises();

            expect(alert).toHaveBeenCalledWith('Server says no<br>Image did not upload.');
        });
    });

    describe('uploading from a url', () => {
        const openUrlModal = async (wrapper: ReturnType<typeof mount>) => {
            withResourcesModal();
            await button(wrapper, 'From URL').trigger('click');
            // The url form is written into the modal on a 1ms timeout. A timer
            // of the same delay set after it fires after it.
            await new Promise(resolve => setTimeout(resolve, 1));
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
            vi.spyOn(console, 'warn').mockImplementation(() => undefined);
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
        });
    });

    it('binds a declared extra field when passed a whole image record', async () => {
        // In an image list the parent passes the whole record as extra-data,
        // base keys (filename, media…) alongside the extra values.
        const wrapper = mountImage({
            extraFields: { caption: { label: 'Caption', placeholder: 'Caption…' } },
            extraData: { filename: 'foo.jpg', media: 5, thumbnail: '/thumbs/foo.jpg', caption: 'Old caption' },
        });
        await nextTick();

        expect(value(wrapper, 'input[name="fields[image][caption]"]')).toBe('Old caption');
    });

    it('keeps typed alt text and extra field values for the form', async () => {
        const wrapper = mountImage({
            extraFields: { caption: { label: 'Caption' } },
            extraData: { caption: 'Old caption' },
        });

        await wrapper.find(altInput).setValue('A better description');
        await wrapper.find('input[name="fields[image][caption]"]').setValue('New caption');

        expect(value(wrapper, altInput)).toBe('A better description');
        expect(value(wrapper, 'input[name="fields[image][caption]"]')).toBe('New caption');
    });

    it('locks page scrolling while the preview lightbox is open', async () => {
        vi.mocked(baguetteBox.run).mockClear();
        vi.mocked(noScroll.on).mockClear();
        vi.mocked(noScroll.off).mockClear();
        mountImage();
        await nextTick();

        expect(baguetteBox.run).toHaveBeenCalledTimes(1);
        const [selector, options] = vi.mocked(baguetteBox.run).mock.calls[0];
        expect(selector).toBe('.editor__image--preview');

        options!.afterShow!();
        expect(noScroll.on).toHaveBeenCalledTimes(1);
        options!.afterHide!();
        expect(noScroll.off).toHaveBeenCalledTimes(1);
    });

    it('starts no lightbox when there is no image', async () => {
        vi.mocked(baguetteBox.run).mockClear();
        mountImage({ filename: '' });
        await nextTick();

        expect(baguetteBox.run).not.toHaveBeenCalled();
    });

    describe('picking a file', () => {
        const file = new File(['bytes'], 'picked.jpg', { type: 'image/jpeg' });
        const fileInput = (wrapper: ReturnType<typeof mount>) => wrapper.find('input[type="file"]');
        const pick = (wrapper: ReturnType<typeof mount>, files: File[]) => {
            Object.defineProperty(fileInput(wrapper).element, 'files', { value: files, configurable: true });
            return fileInput(wrapper).trigger('change');
        };

        it('opens the file picker from the upload button', async () => {
            const wrapper = mountImage();
            const click = vi.spyOn(fileInput(wrapper).element as HTMLInputElement, 'click');

            await button(wrapper, 'Upload').trigger('click');

            expect(click).toHaveBeenCalledTimes(1);
        });

        it('uploads the picked file', async () => {
            vi.mocked(Axios.post).mockResolvedValue({ data: 'picked.jpg' });
            const wrapper = mountImage();

            await pick(wrapper, [file]);
            await flushPromises();

            expect((vi.mocked(Axios.post).mock.calls[0][1] as FormData).get('image')).toBe(file);
            expect(value(wrapper, filenameInput)).toBe('picked.jpg');
        });

        it('does nothing when the picker is cancelled', async () => {
            const wrapper = mountImage();

            await pick(wrapper, []);

            expect(Axios.post).not.toHaveBeenCalled();
        });

        it('does not upload without an upload directory', async () => {
            const wrapper = mountImage({ directory: '' });

            await pick(wrapper, [file]);

            expect(Axios.post).not.toHaveBeenCalled();
        });
    });

    it('ignores a drop that carries no file', async () => {
        const wrapper = mountImage();

        await wrapper.find('.editor__image').trigger('drop', { dataTransfer: { files: [] } });

        expect(Axios.post).not.toHaveBeenCalled();
    });

    it('offers no url upload without an upload endpoint', async () => {
        const wrapper = mountImage({ directoryurl: '' });
        withResourcesModal();

        await button(wrapper, 'From URL').trigger('click');
        await new Promise(resolve => setTimeout(resolve, 1));

        expect(document.querySelector('input[name=from-url-input]')).toBeNull();
    });

    it('resets the modal once the url upload dialog is closed', async () => {
        const wrapper = mountImage();
        withResourcesModal();
        await button(wrapper, 'From URL').trigger('click');

        document.getElementById('resourcesModal')!.dispatchEvent(new Event('hidden.bs.modal'));

        expect(resetModalContent).toHaveBeenCalledWith(labels);
    });

    it('omits optional attributes when they are unset or false', () => {
        // Twig passes `false` for an unset pattern, placeholder or error message.
        const wrapper = mountImage({ pattern: false, placeholder: false, errormessage: false });

        expect(wrapper.find(filenameInput).attributes('data-errormessage')).toBeUndefined();
        expect(wrapper.find(altInput).attributes('pattern')).toBeUndefined();
        expect(wrapper.find(altInput).attributes('placeholder')).toBe(labels.placeholder_alt_text);
    });

    it('binds the optional attributes where they apply when set', () => {
        const wrapper = mountImage({ pattern: '[a-z ]+', errormessage: 'Pick an image' });

        expect(wrapper.find(filenameInput).attributes('data-errormessage')).toBe('Pick an image');
        expect(wrapper.find(altInput).attributes('pattern')).toBe('[a-z ]+');
    });

    it('starts empty without a filename, extra fields or extra data', () => {
        const wrapper = mountImage({ filename: undefined, extraFields: undefined, extraData: undefined });

        expect(value(wrapper, filenameInput)).toBe('');
        expect(wrapper.findAll('input[type="text"]')).toHaveLength(2);
    });

    it('offers a way back up out of a subdirectory', async () => {
        vi.mocked(Axios.get).mockResolvedValue({
            data: [{ text: 'b.jpg', group: 'files', value: 'files/sub/b.jpg', base_url_path: '/bolt/async/filelist' }],
        });
        const wrapper = mountImage();
        withResourcesModal();

        await button(wrapper, 'From library').trigger('click');
        await flushPromises();

        const up = document.querySelector('.modal-body a.directory') as HTMLAnchorElement;
        expect(up.href).toContain('location=files');
        expect(up.href).toContain('type=images');
    });

    it('renders an empty modal for a folder with nothing in it', async () => {
        vi.mocked(Axios.get).mockResolvedValue({ data: [] });
        const wrapper = mountImage();
        withResourcesModal();

        await button(wrapper, 'From library').trigger('click');
        await flushPromises();

        expect(document.querySelector('.modal-body')!.innerHTML).toBe('');
    });

    it('measures progress against what was sent when the total is unknown', async () => {
        let reportProgress!: (event: { loaded: number; total?: number }) => void;
        vi.mocked(Axios.post).mockImplementation((_url, _body, config) => {
            reportProgress = (config as { onUploadProgress: typeof reportProgress }).onUploadProgress;
            return new Promise(() => undefined) as never;
        });
        const wrapper = mountImage();
        const file = new File(['bytes'], 'new.jpg', { type: 'image/jpeg' });
        await wrapper.find('.editor__image').trigger('drop', { dataTransfer: { files: [file] } });

        reportProgress({ loaded: 40 });
        await nextTick();
        expect(wrapper.find('.progress-bar').attributes('aria-valuenow')).toBe('100');

        reportProgress({ loaded: 0, total: 0 });
        await nextTick();
        expect(wrapper.find('.progress').exists()).toBe(false);
    });

    it('shows progress while fetching from a url', async () => {
        let reportProgress!: (event: { loaded: number; total?: number }) => void;
        vi.mocked(Axios.post).mockImplementation((_url, _body, config) => {
            reportProgress = (config as { onUploadProgress: typeof reportProgress }).onUploadProgress;
            return new Promise(() => undefined) as never;
        });
        const wrapper = mountImage();
        withResourcesModal();
        await button(wrapper, 'From URL').trigger('click');
        await new Promise(resolve => setTimeout(resolve, 1));
        (document.querySelector('input[name=from-url-input]') as HTMLInputElement).value = 'https://example.org/a.jpg';
        (document.getElementById('modalButtonAccept') as HTMLButtonElement).click();

        reportProgress({ loaded: 30, total: 60 });
        await nextTick();

        expect(wrapper.find('.progress-bar').attributes('aria-valuenow')).toBe('50');
    });

    it('does nothing for a url upload when the page has no modal', async () => {
        const wrapper = mountImage();

        await button(wrapper, 'From URL').trigger('click');
        await new Promise(resolve => setTimeout(resolve, 1));

        expect(Axios.post).not.toHaveBeenCalled();
        expect(renable).not.toHaveBeenCalled();
    });
});
